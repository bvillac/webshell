<?php
include('cls_Base.php');//para HTTP
include('cls_Global.php');//para HTTP
include('EMPRESA.php');//para HTTP
include('VSValidador.php');
include('VSClaveAcceso.php');
class NubeGuiaRemision {
    //put your code here
    private function buscarGuias($op,$NumPed) {
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            $conCont = $obj_con->conexionServidor();
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnv=$obj_var->limitEnv;
            //$sql = "SELECT TIP_NOF,CONCAT(REPEAT('0',9-LENGTH(RIGHT(NUM_NOF,9))),RIGHT(NUM_NOF,9)) NUM_NOF,
            
            switch ($op) {
                Case 1://Compras alimenta inventarios
                    $sql = "SELECT A.NUM_GUI,A.FEC_GUI,A.TIP_NOF,A.NUM_NOF,A.FEC_VTA,A.FEC_I_T,A.FEC_T_T,A.MOT_TRA,A.PUN_PAR,
                            A.PUN_LLE,A.FEC_PAR,A.COD_CLI,A.NOM_CLI,A.CED_RUC,A.COD_TRA,A.NOM_TRA,A.C_R_TRA,A.USUARIO,
                            B.DIR_CLI
                            FROM " .  $obj_con->BdServidor . ".IG0045 A
                                INNER JOIN " .  $obj_con->BdServidor . ".MG0031 B
                                    ON A.COD_CLI=B.COD_CLI
                    WHERE A.IND_UPD='L' AND A.FEC_GUI>='$fechaIni' AND A.ENV_DOC='0' LIMIT $limitEnv ";
                    break;
               
                Case 3://Compras provisiones de pasivos por NUmero
                    $sql = "SELECT A.TIP_PED,A.NUM_PED,A.FEC_PED,A.COD_PRO,A.NOM_PRO,A.DIR_PRO,A.N_S_PRO,A.N_F_PRO,A.F_F_PRO,A.COD_SUS,
                                        A.COD_I_P,A.BAS_IV0,A.BAS_IVA,A.VAL_IVA,A.TIP_RET,A.NUM_RET,A.POR_RET,A.VAL_RET,A.TIP_RE1,A.BAS_RE1,
                                        A.POR_RE1,A.VAL_RE1,A.P_R_IVA,A.V_R_IVA,A.FEC_RET,A.DET_RET,B.CED_RUC,A.USUARIO,B.TEL_N01,B.CORRE_E
                                     FROM " .  $obj_con->BdServidor . ".IG0054 A
                                             INNER JOIN " .  $obj_con->BdServidor . ".MG0032 B
                                                     ON B.COD_PRO=A.COD_PRO
                             WHERE A.TIP_PED='PP' AND A.IND_UPD='L' AND A.NUM_PED='$NumPed'  ";
                    break;
                default:
                    $sql = "";
            }
            //echo $sql;
            $sentencia = $conCont->query($sql);
            if ($sentencia->num_rows > 0) {
                while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                    $rawData[] = $fila;
                }
            }
            $conCont->close();
            return $rawData;
        } catch (Exception $e) {
            echo $e;
            $conCont->close();
            return false;
        }
    }
    
    public function insertarDocumentosGuia($op,$NumPed) {
        
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionIntermedio();
        $objEmpData= new EMPRESA();
        /****VARIBLES DE SESION*******/
        $emp_id=$obj_var->emp_id;
        $est_id=$obj_var->est_id;
        $pemi_id=$obj_var->pemi_id;
        try {
            $cabDoc = $this->buscarGuias($op,$NumPed);//Guias de Remision
            $empresaEnt=$objEmpData->buscarDataEmpresa($emp_id,$est_id,$pemi_id);//recuperar info deL Contribuyente
            $codDoc='06';//GUIAS DE REMISION
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                $this->InsertarCabGuia($con,$obj_con,$cabDoc, $empresaEnt,$codDoc, $i);
                $idCab = $con->insert_id;
                $this->InsertarDestinatarioGuia($con,$obj_con,$cabDoc,$empresaEnt,$idCab,$i);
                //$this->InsertarDetGuia($con,$obj_con,$cabDoc,$idCab);
                //$this->InsertarRetenDatoAdicional($con,$obj_con,$i,$cabDoc,$idCab);
            }
            $con->commit();
            $con->close();
            $this->actualizaErpCabCompras($cabDoc);
            echo "ERP Actualizado";
            return true;
        } catch (Exception $e) {
            //$trans->rollback();
            //$con->active = false;
            $con->rollback();
            $con->close();
            throw $e;
            return false;
        }   
    }
    
    private function InsertarCabGuia($con,$obj_con, $objEnt, $objEmp, $codDoc, $i) {
        $valida = new VSValidador();
        $tip_iden = $valida->tipoIdent($objEnt[$i]['CED_RUC']);
        $Secuencial = $valida->ajusteNumDoc($objEnt[$i]['NUM_GUI'], 9);
        $ced_ruc = ($tip_iden == '07') ? '9999999999999' : $objEnt[$i]['CED_RUC'];
        /* Datos para Genera Clave */
        //$tip_doc,$fec_doc,$ruc,$ambiente,$serie,$numDoc,$tipoemision
        $objCla = new VSClaveAcceso();
        $serie = $objEmp['Establecimiento'] . $objEmp['PuntoEmision'];
        $fec_doc = date("Y-m-d", strtotime($objEnt[$i]['FEC_GUI']));
        //$perFiscal = date("m/Y", strtotime($objEnt[$i]['FEC_GUI']));
        $ClaveAcceso = $objCla->claveAcceso($codDoc, $fec_doc, $objEmp['Ruc'], $objEmp['Ambiente'], $serie, $Secuencial, $objEmp['TipoEmision']);
        /** ********************** */
        $razonSocialDoc=str_replace("'","`",$objEnt[$i]['NOM_PRO']);// Error del ' en el Text se lo Reemplaza `
        //$nomCliente=$objEnt[$i]['NOM_PRO'];// Error del ' en el Text
        
        //DATOS IMPORTANTES DE GUIA
        $DireccionEstablecimiento=$objEmp['DireccionMatriz'];
        $puntoPartida=$objEmp['DireccionMatriz'];//Direecion de partida de la GUia
        $RazonSocialTransportista=$objEnt[$i]['NOM_TRA'];
        $TipoIdentificacionTransportista=(strlen($objEnt[$i]['C_R_TRA'])>0)?$valida->tipoIdent($objEnt[$i]['C_R_TRA']):'';//Verifica si Existen Datos en Cedula Ruc del TRansportista
        $Rise="";//Verificar cuando es RISE
        $Placa="";
        $NombreDocumento='GU';
        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeGuiaRemision
                (Ambiente,TipoEmision,RazonSocial,NombreComercial,Ruc,ClaveAcceso,CodigoDocumento,Establecimiento,PuntoEmision,
                 Secuencial,DireccionMatriz,DireccionEstablecimiento,DireccionPartida,RazonSocialTransportista,
                 TipoIdentificacionTransportista,IdentificacionTransportista,Rise,ObligadoContabilidad,ContribuyenteEspecial,
                 FechaInicioTransporte,FechaFinTransporte,Placa,UsuarioCreador,NombreDocumento,SecuencialERP,Estado)VALUES(
                '" . $objEmp['Ambiente'] . "',
                '" . $objEmp['TipoEmision'] . "',
                '" . $objEmp['RazonSocial'] . "',
                '" . $objEmp['NombreComercial'] . "',
                '" . $objEmp['Ruc'] . "',
                '$ClaveAcceso',
                '$codDoc',
                '" . $objEmp['Establecimiento'] . "',
                '" . $objEmp['PuntoEmision'] . "',
                '$Secuencial',
                '" . $objEmp['DireccionMatriz'] . "',
                '$DireccionEstablecimiento',
                '$puntoPartida',
                '$RazonSocialTransportista',
                '$TipoIdentificacionTransportista',
                '$Rise',
                '" . $objEmp['ObligadoContabilidad'] . "', 
                '" . $objEmp['ContribuyenteEspecial'] . "',
                '" . $objEnt[$i]['FEC_I_T'] . "',
                '" . $objEnt[$i]['FEC_T_T'] . "',
                '$Placa',
                '" . $objEnt[$i]['USUARIO'] . "',
                '$NombreDocumento',
                '$Secuencial','1')";
        $command = $con->prepare($sql);
        $command->execute();

    }
    
    private function InsertarDestinatarioGuia($con,$obj_con, $cabDoc,$objEmp, $idCab,$i) {
        $valida = new VSValidador();
        //Datos Destinatario
        $MotivoTraslado=$this->motivoTransporte($cabDoc[$i]['MOT_TRA']);
        $DocAduaneroUnico='';//Obligatorio cuando correponda
        $CodEstabDestino='001';//Obligatorio cuando correponda
        $Ruta='';//Obligatorio cuando correponda
        $CodDocSustento='';
        $NumDocSustento='';
        $NumAutDocSustento='';
        $FechaEmisionDocSustento='';
        //Solo Ingresa cuando el tipo es F4 ose factura 
        IF($cabDoc[$i]['TIP_NOF']=='F4'){//Estos Datos son Obligatorios si el Doc es una Factura
            $serie = $objEmp['Establecimiento'] .'-'. $objEmp['PuntoEmision'];
            $CodDocSustento=($cabDoc[$i]['TIP_NOF']=='F4')?'01':'';//Obligatorio cuando correponda dependiendo Doc FACT, NC,ND,RE tABLA 4
            $NumDocSustento=$serie.'-'.$valida->ajusteNumDoc($cabDoc[$i]['NUM_NOF'], 9);//Obligatorio cuando correponda Formato  002-001-000000001
            $NumAutDocSustento='';//Autorizacon por SRI eje 2110201116302517921467390011234567891
            $FechaEmisionDocSustento='';//Fecha de Autorizacion del DOc 21/10/2011
        }
       
        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeGuiaRemisionDestinatario
                (IdentificacionDestinatario,RazonSocialDestinatario,DirDestinatario,
                MotivoTraslado,DocAduaneroUnico,CodEstabDestino,Ruta,CodDocSustento,NumDocSustento,NumAutDocSustento,
                FechaEmisionDocSustento,IdGuiaRemision)VALUES(
                '" . $cabDoc[$i]['CED_RUC'] . "',
                '" . $cabDoc[$i]['NOM_CLI'] . "',
                '" . $cabDoc[$i]['DIR_CLI'] . "',
                '$MotivoTraslado',
                '$DocAduaneroUnico',
                '$Ruta',
                '$CodDocSustento',
                '$NumDocSustento',
                '$NumAutDocSustento',
                '$FechaEmisionDocSustento',
                '$idCab') ";
        $command = $con->prepare($sql);
        $command->execute();
        $idDestino = $con->insert_id;

    }
    
    private function InsertarDetGuia($con,$obj_con, $detFact, $idCab) {
        
    }
    
    private function InsertarDetGuiaxxx($con,$obj_con, $detFact, $idCab) {
        $valSinImp = 0;
        $val_iva12 = 0;
        $vet_iva12 = 0;
        $val_iva0 = 0;
        $vet_iva0 = 0;
        //TIP_NOF,NUM_NOF,FEC_VTA,COD_ART,NOM_ART,CAN_DES,P_VENTA,T_VENTA,VAL_DES,I_M_IVA,VAL_IVA
        for ($i = 0; $i < sizeof($detFact); $i++) {
            $valSinImp = floatval($detFact[$i]['T_VENTA']) - floatval($detFact[$i]['VAL_DES']);
            if ($detFact[$i]['I_M_IVA'] == '1') {
                $val_iva12 = $val_iva12 + floatval($detFact[$i]['VAL_IVA']);
                $vet_iva12 = $vet_iva12 + $valSinImp;
            } else {
                $val_iva0 = 0;
                $vet_iva0 = $vet_iva0 + $valSinImp;
            }

            $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDetalleFactura 
                    (CodigoPrincipal,CodigoAuxiliar,Descripcion,Cantidad,PrecioUnitario,Descuento,PrecioTotalSinImpuesto,IdFactura) VALUES (
                    '" . $detFact[$i]['COD_ART'] . "', 
                    '1',
                    '" . $detFact[$i]['NOM_ART'] . "', 
                    '" . $detFact[$i]['CAN_DES'] . "',
                    '" . $detFact[$i]['P_VENTA'] . "',
                    '" . $detFact[$i]['VAL_DES'] . "',
                    '$valSinImp',
                    '$idCab')";
            $command = $con->prepare($sql);
            $command->execute();
            $idDet = $con->insert_id;
            if ($detFact[$i]['I_M_IVA'] == '1') {//Verifico si el ITEM tiene Impuesto
                //Segun Datos Sri
                $this->InsertarDetImpFactura($con,$obj_con, $idDet, '2', '2', '12', $valSinImp, $detFact[$i]['VAL_IVA']); //12%
            } else {//Caso Contrario no Genera Impuesto
                $this->InsertarDetImpFactura($con,$obj_con, $idDet, '2', '0', '0', $valSinImp, $detFact[$i]['VAL_IVA']); //0%
            }
        }
        //Insertar Datos de Iva 0%
        If ($vet_iva0 > 0) {
            $this->InsertarFacturaImpuesto($con,$obj_con, $idCab, '2', '0', '0', $vet_iva0, $val_iva0);
        }
        //Inserta Datos de Iva 12
        If ($vet_iva12 > 0) {
            $this->InsertarFacturaImpuesto($con,$obj_con, $idCab, '2', '2', '12', $vet_iva12, $val_iva12);
        }
    }
    
    Private Function motivoTransporte($op){
        $motivo  = "";
        
        switch ($op) {
            Case 1:
                $motivo = "VENTA";
                break;
            Case 2:
                $motivo = "COMPRA";
                break;
            Case 3:
                $motivo = "TRANSFORMACIÓN";
                break;
            Case 4:
                $motivo = "CONSIGNACIÓN";
                break;
            Case 5;
                $motivo = "DEVOLUCIÓN";
                break;
            Case 6:
                $motivo = "IMPORTACIÓN";
                break;
            Case 7:
                $motivo = "EXPORTACIÓN";
                break;
            Case 8:
                $motivo = "TRASLADO ENTRE ESTABLECIMIENTOS DE UNA MISMA EMPRESA";
                break;
            Case 9:
                $motivo = "TRASLADO POR EMISOR ITINERANTE DE COMPROBANTES DE VENTA";
                break;
            default:
                $motivo = "OTROS";
        }
        Return motivo;
    }
    
}
