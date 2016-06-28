<?php
include('cls_Base.php');//para HTTP
include('cls_Global.php');//para HTTP
include('EMPRESA.php');//para HTTP
include('VSValidador.php');
include('VSClaveAcceso.php');
include('mailSystem.php');
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
                Case 1://Consulta Masiva
                    $sql = "SELECT A.NUM_GUI,A.FEC_GUI,A.TIP_NOF,A.NUM_NOF,A.FEC_VTA,A.FEC_I_T,A.FEC_T_T,A.MOT_TRA,A.PUN_PAR,
                            A.PUN_LLE,A.FEC_PAR,A.COD_CLI,A.NOM_CLI,A.CED_RUC,A.COD_TRA,A.NOM_TRA,A.C_R_TRA,A.USUARIO,
                            B.DIR_CLI,B.NOM_CTO,B.CORRE_E,A.PLK_TRA,A.ATIENDE,'' ID_DOC
                            FROM " .  $obj_con->BdServidor . ".IG0045 A
                                INNER JOIN " .  $obj_con->BdServidor . ".MG0031 B
                                    ON A.COD_CLI=B.COD_CLI
                    WHERE A.IND_UPD='L' AND A.FEC_GUI>='$fechaIni' AND A.ENV_DOC='0' LIMIT $limitEnv ";
                    break;
               
                Case 2://Consulta por un Numero Determinado
                    $sql = "SELECT A.NUM_GUI,A.FEC_GUI,A.TIP_NOF,A.NUM_NOF,A.FEC_VTA,A.FEC_I_T,A.FEC_T_T,A.MOT_TRA,A.PUN_PAR,
                            A.PUN_LLE,A.FEC_PAR,A.COD_CLI,A.NOM_CLI,A.CED_RUC,A.COD_TRA,A.NOM_TRA,A.C_R_TRA,A.USUARIO,
                            B.DIR_CLI,B.NOM_CTO,B.CORRE_E,A.PLK_TRA,A.ATIENDE,'' ID_DOC
                            FROM " .  $obj_con->BdServidor . ".IG0045 A
                                INNER JOIN " .  $obj_con->BdServidor . ".MG0031 B
                                    ON A.COD_CLI=B.COD_CLI
                    WHERE A.IND_UPD='L' AND A.NUM_GUI='$NumPed' ";
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
    
    private function buscarDetGuia($tipDoc, $numDoc) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        $rawData = array();
        $sql = "SELECT A.COD_ART,A.NOM_ART,A.CAN_DES,A.COD_LIN
                        FROM " . $obj_con->BdServidor . ".IG0046 A
                WHERE NUM_GUI='$numDoc'";
        //echo $sql;
        $sentencia = $conCont->query($sql);
        if ($sentencia->num_rows > 0) {
            while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                $rawData[] = $fila;
            }
        }

        $conCont->close();
        return $rawData;
    }
    
    public function insertarDocumentosGuia($op,$NumPed) {
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionIntermedio();
        $objEmpData= new EMPRESA();
        $dataMail = new mailSystem();
        /****VARIBLES DE SESION*******/
        $emp_id=$obj_var->emp_id;
        $est_id=$obj_var->est_id;
        $pemi_id=$obj_var->pemi_id;
        try {
            $cabDoc = $this->buscarGuias($op,$NumPed);//Guias de Remision
            $empresaEnt=$objEmpData->buscarDataEmpresa($emp_id,$est_id,$pemi_id);//recuperar info deL Contribuyente
            $codDoc='06';//GUIAS DE REMISION
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                $this->InsertarCabGuia($con,$obj_con,$obj_var,$cabDoc, $empresaEnt,$codDoc, $i);
                $idCab = $con->insert_id;
                $this->InsertarDestinatarioGuia($con,$obj_con,$obj_var,$cabDoc,$empresaEnt,$idCab,$i);
                $idDestino = $con->insert_id;
                $detDoc=$this->buscarDetGuia($obj_var->tipoGuiLocal,$cabDoc[$i]['NUM_GUI']);
                $this->InsertarDetGuia($con,$obj_con,$obj_var,$detDoc,$idDestino);
                //Descomentar si se desea Agregar Datos Adicional
                $this->InsertarGuiaDatoAdicional($con,$obj_con,$obj_var,$i,$cabDoc,$idCab);
                $cabDoc[$i]['ID_DOC']=$idCab;//Actualiza el IDs Documento Autorizacon SRI
            }
            $con->commit();
            $con->close();
            $this->actualizaErpCabGuia($cabDoc);
            //echo "ERP Actualizado";
            return true;
        } catch (Exception $e) {
            $con->rollback();
            $con->close();
            //throw $e;
            //$DocData["tipo"] = $obj_var->tipoGuiLocal;
            //$DocData["NumDoc"] = $cabDoc[$i]['NUM_GUI'];
            //$DocData["Error"] = $e;
            //$dataMail->enviarMailError($DocData);
            return false;
        }   
    }
    
    
    private function InsertarCabGuia($con,$obj_con,$obj_var, $objEnt, $objEmp, $codDoc, $i) {
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
        $razonSocialDoc=$obj_var->limpioCaracteresSQL($objEnt[$i]['NOM_CLI']);// Error del ' en el Text se lo Reemplaza `
        //$nomCliente=$objEnt[$i]['NOM_PRO'];// Error del ' en el Text
        
        //DATOS IMPORTANTES DE GUIA OBLIGATORIOS
        $DireccionEstablecimiento=$objEmp['DireccionMatriz'];
        $puntoPartida=$objEmp['DireccionMatriz'];//Direecion de partida de la GUia
        $RazonSocialTransportista=(strlen($objEnt[$i]['NOM_TRA'])>0)?$objEnt[$i]['NOM_TRA']:'Transporte Empresa '.$objEmp['RazonSocial'];//Si no hay transporte Adjunta Nombre de la Empresa
        $TipoIdentificacionTransportista=(strlen($objEnt[$i]['C_R_TRA'])>0)?$valida->tipoIdent($objEnt[$i]['C_R_TRA']):'05';//Verifica si Existen Datos en Cedula Ruc del TRansportista
        //Valida que la Identificacion sean numeros
        if(is_numeric($objEnt[$i]['C_R_TRA'])){
            $IdentificacionTransportista=(strlen($objEnt[$i]['C_R_TRA'])>0)?trim($objEnt[$i]['C_R_TRA']):'9999999999';
        }else{
            $IdentificacionTransportista='9999999999';
        }
        
        $Rise="";//Verificar cuando es RISE
        $Placa=(strlen($objEnt[$i]['PLK_TRA'])>0)?trim($objEnt[$i]['PLK_TRA']):'Utimpor';//$objEnt[$i]['PLK_TRA'];//Dato Obligatorio
        $NombreDocumento=$obj_var->tipoGuiLocal;
        /*Configuracion para Usuario ATIENDE, se reempla la v16->16 ->20-08-2015
         * es decir solo para usuario Utimpor que en la tablas guarda la V16,V03 etc
         */
        $Atiende=$objEnt[$i]['ATIENDE'];//str_replace("v","",$objEnt[$i]['USUARIO']);
        //*****************************************************
        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeGuiaRemision
                (Ambiente,TipoEmision,RazonSocial,NombreComercial,Ruc,ClaveAcceso,CodigoDocumento,Establecimiento,PuntoEmision,
                 Secuencial,DireccionMatriz,DireccionEstablecimiento,DireccionPartida,RazonSocialTransportista,
                 TipoIdentificacionTransportista,IdentificacionTransportista,Rise,ObligadoContabilidad,ContribuyenteEspecial,
                 FechaInicioTransporte,FechaFinTransporte,Placa,UsuarioCreador,FechaEmisionErp,NombreDocumento,SecuencialERP,Estado,FechaCarga)VALUES(
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
                '$IdentificacionTransportista',
                '$Rise',
                '" . $objEmp['ObligadoContabilidad'] . "', 
                '" . $objEmp['ContribuyenteEspecial'] . "',
                '" . $objEnt[$i]['FEC_I_T'] . "',
                '" . $objEnt[$i]['FEC_T_T'] . "',
                '$Placa',
                '$Atiende',
                '" . $objEnt[$i]['FEC_GUI'] . "',
                '$NombreDocumento',
                '$Secuencial','1',CURRENT_TIMESTAMP() )";
        $command = $con->prepare($sql);
        $command->execute();

    }
    
    private function InsertarDestinatarioGuia($con,$obj_con,$obj_var,$cabDoc,$objEmp, $idCab,$i) {
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
        $RazonSocialDestinatario=$obj_var->limpioCaracteresSQL($cabDoc[$i]['NOM_CLI']);// Error del ' en el Text se lo Reemplaza 
        //Solo Ingresa cuando el tipo es F4 ose factura 
        IF($cabDoc[$i]['TIP_NOF']==$obj_var->tipoFacLocal){//Estos Datos son Obligatorios si el Doc es una Factura
            $serie = $objEmp['Establecimiento'] .'-'. $objEmp['PuntoEmision'];
            $CodDocSustento=($cabDoc[$i]['TIP_NOF']==$obj_var->tipoFacLocal)?'01':'';//Obligatorio cuando correponda dependiendo Doc FACT, NC,ND,RE tABLA 4
            $NumDocSustento=$serie.'-'.$valida->ajusteNumDoc($cabDoc[$i]['NUM_NOF'], 9);//Obligatorio cuando correponda Formato  002-001-000000001
            $NumAutDocSustento='';//Autorizacon por SRI eje 2110201116302517921467390011234567891
            $FechaEmisionDocSustento='';//Fecha de Autorizacion del DOc 21/10/2011
        }
       
        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeGuiaRemisionDestinatario
                (IdentificacionDestinatario,RazonSocialDestinatario,DirDestinatario,
                MotivoTraslado,DocAduaneroUnico,CodEstabDestino,Ruta,CodDocSustento,NumDocSustento,NumAutDocSustento,
                FechaEmisionDocSustento,IdGuiaRemision)VALUES(
                '" . $cabDoc[$i]['CED_RUC'] . "',
                '$RazonSocialDestinatario',
                '" . $cabDoc[$i]['DIR_CLI'] . "',
                '$MotivoTraslado',
                '$DocAduaneroUnico',
                '$CodEstabDestino',
                '$Ruta',
                '$CodDocSustento',
                '$NumDocSustento',
                '$NumAutDocSustento',
                '$FechaEmisionDocSustento',
                '$idCab') ";
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarDetGuia($con,$obj_con,$obj_var, $detDoc, $idCab) {
        for ($i = 0; $i < sizeof($detDoc); $i++) {
            $CodigoAdicional='';
            $Descripcion=$obj_var->limpioCaracteresSQL($detDoc[$i]['NOM_ART']);
            $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeGuiaRemisionDetalle
                    (CodigoInterno,CodigoAdicional,Descripcion,Cantidad,IdGuiaRemisionDestinatario)VALUES(
                    '" . $detDoc[$i]['COD_ART'] . "',
                    '$CodigoAdicional',
                    '$Descripcion',
                    '" . $detDoc[$i]['CAN_DES'] . "',
                    '$idCab') ";
            $command = $con->prepare($sql);
            $command->execute();
            $idDet = $con->insert_id;
            //Descomentar si se desea guardar Datos Adicionales
            //$this->InsertarGuiaDetDatoAdicional($con,$obj_con,$i,$detDoc,$idDet);
        }
    }
    
    private function InsertarGuiaDatoAdicional($con,$obj_con,$obj_var,$i, $cabDoc, $idCab) {
        $direccion = $cabDoc[$i]['DIR_CLI'];
        $correo = $cabDoc[$i]['CORRE_E'];
        $contacto = $cabDoc[$i]['NOM_CTO'];
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDatoAdicionalGuiaRemision 
                 (Nombre,Descripcion,IdGuiaRemision)
                 VALUES
                 ('Direccion','$direccion','$idCab'),('Correo','$correo','$idCab'),('Contacto','$contacto','$idCab')";
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarGuiaDetDatoAdicional($con,$obj_con, $i, $detDoc, $idDet) {
        $direccion = $detDoc[$i]['COD_LIN'];
        $telefono = $detDoc[$i]['COD_LIN'];
        $correo = $detDoc[$i]['COD_LIN'];
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDatoAdicionalGuiaRemisionDetalle 
                 (Nombre,Descripcion,IdGuiaRemisionDetalle)
                 VALUES
                 ('Direccion','$direccion','$idDet'),('Telefono','$telefono','$idDet'),('Correo','$correo','$idDet')";
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function actualizaErpCabGuia($cabFact) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        try {
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $numero = $cabFact[$i]['NUM_GUI'];
                $tipo = $cabFact[$i]['TIP_NOF'];
                $ids=$cabFact[$i]['ID_DOC'];//Contine el IDs del Tabla Autorizacion
                $sql = "UPDATE " . $obj_con->BdServidor . ".IG0045 SET ENV_DOC='$ids'
                        WHERE NUM_GUI='$numero' AND IND_UPD='L'";
                //echo $sql;
                $command = $conCont->prepare($sql);
                $command->execute();
            }
            $conCont->commit();
            $conCont->close();
            return true;
        } catch (Exception $e) {
            $conCont->rollback();
            $conCont->close();
            throw $e;
            return false;
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
        Return $motivo;
    }
    
}
