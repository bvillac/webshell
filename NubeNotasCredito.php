<?php
//include('whebshell/Mod/Base/cls_Base.php');
include('cls_Base.php');//para HTTP
include('cls_Global.php');//para HTTP
include('EMPRESA.php');//para HTTP
include('VSValidador.php');
include('VSClaveAcceso.php');
include('mailSystem.php');
class NubeNotasCredito {
    private $tipDev="DV";
    
    private function buscarNC($op,$NumPed) {
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            $conCont = $obj_con->conexionServidor();
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnv=$obj_var->limitEnv;
            
            switch ($op) {
                Case 1://Devoluciones Cabecera
                    $sql = "SELECT A.TIP_DEV,A.NUM_DEV,A.FEC_DEV,A.COD_MOT,C.NOM_MOT,A.TIP_NOF,A.NUM_NOF,A.FEC_NOF,
                                    A.COD_CLI,B.CED_RUC,B.NOM_CLI,B.DIR_CLI,B.TEL_N01,B.CORRE_E,A.VAL_BRU,A.POR_DES,A.VAL_DES,
                                    A.BAS_IVA,A.BAS_IV0,A.POR_IVA,A.POR_IVA,A.VAL_IVA,A.VAL_NET,A.POR_RET,
                                    A.VAL_RET,A.T_I_DEV,A.T_P_DEV,A.LIN_N01,A.ATIENDE,A.USUARIO,'' ID_DOC
                                    FROM " .  $obj_con->BdServidor . ".IG0060 A
                                            INNER JOIN " .  $obj_con->BdServidor . ".MG0031 B ON A.COD_CLI=B.COD_CLI 
                                            INNER JOIN " .  $obj_con->BdServidor . ".MG0041 C ON A.COD_MOT=C.COD_MOT
                            WHERE A.TIP_DEV='".$this->tipDev."' AND A.FEC_DEV>='$fechaIni' AND A.IND_UPD='L' AND A.ENV_DOC='0' LIMIT $limitEnv";
                    
                    break;
                Case 2://Compras provisiones de pasivos por NUmero
                    
                    $sql = "SELECT A.TIP_DEV,A.NUM_DEV,A.FEC_DEV,A.COD_MOT,C.NOM_MOT,A.TIP_NOF,A.NUM_NOF,A.FEC_NOF,
                                    A.COD_CLI,B.CED_RUC,B.NOM_CLI,B.DIR_CLI,B.TEL_N01,B.CORRE_E,A.VAL_BRU,A.POR_DES,A.VAL_DES,
                                    A.BAS_IVA,A.BAS_IV0,A.POR_IVA,A.POR_IVA,A.VAL_IVA,A.VAL_NET,A.POR_RET,
                                    A.VAL_RET,A.T_I_DEV,A.T_P_DEV,A.LIN_N01,A.ATIENDE,A.USUARIO,'' ID_DOC
                                    FROM " .  $obj_con->BdServidor . ".IG0060 A
                                            INNER JOIN " .  $obj_con->BdServidor . ".MG0031 B ON A.COD_CLI=B.COD_CLI 
                                            INNER JOIN " .  $obj_con->BdServidor . ".MG0041 C ON A.COD_MOT=C.COD_MOT
                            WHERE A.TIP_DEV='".$this->tipDev."' AND A.IND_UPD='L' AND A.NUM_DEV='$NumPed'  ";
                    
                    break;
                default:
                    $sql = "";
            }
            
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

    private function buscarDetNC($tipDoc, $numDoc) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        $rawData = array();        
        $sql = "SELECT A.COD_ART,A.NOM_ART,A.CAN_DEV,A.P_VENTA,A.T_VENTA,A.I_M_IVA,A.VAL_DES,IF(A.I_M_IVA=1,A.T_VENTA*0.12,0) VAL_IVA
                    FROM " . $obj_con->BdServidor . ".IG0061 A
                WHERE A.TIP_DEV='$tipDoc' AND A.NUM_DEV='$numDoc' AND A.IND_EST='L';";
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

    public function insertarDocumentosNC($op,$NumPed) {
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionIntermedio();
        $objEmpData= new EMPRESA();
        /****VARIBLES DE SESION*******/
        $emp_id=$obj_var->emp_id;
        $est_id=$obj_var->est_id;
        $pemi_id=$obj_var->pemi_id;
        try {
            $cabFact = $this->buscarNC($op,$NumPed);
            $empresaEnt=$objEmpData->buscarDataEmpresa($emp_id,$est_id,$pemi_id);//recuperar info deL Contribuyente

            $codDoc='04';//Documento Notas de Credito
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $this->InsertarCabNC($con,$obj_con,$cabFact, $empresaEnt,$codDoc, $i);
                $idCab = $con->insert_id;
                $detFact=$this->buscarDetNC($cabFact[$i]['TIP_DEV'],$cabFact[$i]['NUM_DEV']);
                $this->InsertarDetNC($con,$obj_con,$detFact,$idCab);
                $this->InsertarNcDatoAdicional($con,$obj_con,$i,$cabFact,$idCab);
                $cabFact[$i]['ID_DOC']=$idCab;//Actualiza el IDs Documento Autorizacon SRI
            }
            $con->commit();
            $con->close();
            $this->actualizaErpCabNc($cabFact);
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
    
    private function InsertarCabNC($con,$obj_con, $objEnt, $objEmp, $codDoc, $i) {
        $valida = new VSValidador();
        $tip_iden = $valida->tipoIdent($objEnt[$i]['CED_RUC']);
        $Secuencial = $valida->ajusteNumDoc($objEnt[$i]['NUM_DEV'], 9);
        
        //$GuiaRemi = (strlen($objEnt[$i]['GUI_REM']) > 0) ? $objEmp['Establecimiento'] . '-' . $objEmp['PuntoEmision'] . '-' . $valida->ajusteNumDoc($objEnt[$i]['GUI_REM'], 9) : '';
        $ced_ruc = ($tip_iden == '07') ? '9999999999999' : $objEnt[$i]['CED_RUC'];
        /* Datos para Genera Clave */
        //$tip_doc,$fec_doc,$ruc,$ambiente,$serie,$numDoc,$tipoemision
        $objCla = new VSClaveAcceso();
        $serie = $objEmp['Establecimiento'] . $objEmp['PuntoEmision'];
        $fec_doc = date("Y-m-d", strtotime($objEnt[$i]['FEC_DEV']));
        $ClaveAcceso = $objCla->claveAcceso($codDoc, $fec_doc, $objEmp['Ruc'], $objEmp['Ambiente'], $serie, $Secuencial, $objEmp['TipoEmision']);
        /** ********************** */
        $nomCliente=str_replace("'","`",$objEnt[$i]['NOM_CLI']);// Error del ' en el Text se lo Reemplaza `
        //$nomCliente=$objEnt[$i]['NOM_CLI'];// Error del ' en el Text
        $TotalSinImpuesto=floatval($objEnt[$i]['BAS_IVA'])+floatval($objEnt[$i]['BAS_IV0']);//Cambio por Ajuste de Valores Byron Diferencias
        $Rise="";//Verificar cuando es RISE
        $CodDocMod="01";//Aplica a documentos de Facturas =>f4
        $NumDocMod = $objEmp['Establecimiento'] ."-" .$objEmp['PuntoEmision']."-".$valida->ajusteNumDoc($objEnt[$i]['NUM_NOF'], 9);
        $FecEmiDocMod=date("Y-m-d", strtotime($objEnt[$i]['FEC_NOF']));

        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeNotaCredito
                    (Ambiente,TipoEmision,RazonSocial,NombreComercial,Ruc,ClaveAcceso,CodigoDocumento,Establecimiento,
                     PuntoEmision,Secuencial,DireccionMatriz,FechaEmision,DireccionEstablecimiento,ContribuyenteEspecial,
                     ObligadoContabilidad,TipoIdentificacionComprador,RazonSocialComprador,IdentificacionComprador,Rise,
                     CodDocModificado,NumDocModificado,FechaEmisionDocModificado,TotalSinImpuesto,ValorModificacion,MotivoModificacion,
                     Moneda,SecuencialERP,CodigoTransaccionERP,UsuarioCreador,Estado,FechaCarga) VALUES (
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
                            '$fec_doc', 
                            '" . $objEmp['DireccionMatriz'] . "', 
                            '" . $objEmp['ContribuyenteEspecial'] . "', 
                            '" . $objEmp['ObligadoContabilidad'] . "', 
                            '$tip_iden',          
                            '$nomCliente', 
                            '$ced_ruc','$Rise',
                            '$CodDocMod','$NumDocMod','$FecEmiDocMod', 
                            '" . $TotalSinImpuesto . "',
                            '" . $objEnt[$i]['VAL_NET'] . "',
                            '" . $objEnt[$i]['NOM_MOT'] . "', 
                            '" . $objEmp['Moneda'] . "', 
                            '$Secuencial', 
                            '" . $objEnt[$i]['TIP_DEV'] . "',
                            '" . $objEnt[$i]['ATIENDE'] . "',
                            '1',CURRENT_TIMESTAMP() )";

        //echo $sql;
        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);

    }

    private function InsertarDetNC($con,$obj_con, $detFact, $idCab) {
        //$obj_var = new cls_Global();
        $valSinImp = 0;
        $val_iva12 = 0;
        $vet_iva12 = 0;
        $val_iva0 = 0;//Valor de Iva
        $vet_iva0 = 0;//Venta total con Iva
        //TIP_NOF,NUM_NOF,FEC_VTA,COD_ART,NOM_ART,CAN_DES,P_VENTA,T_VENTA,VAL_DES,I_M_IVA,VAL_IVA
        for ($i = 0; $i < sizeof($detFact); $i++) {
            $valSinImp = floatval($detFact[$i]['T_VENTA']) - floatval($detFact[$i]['VAL_DES']);
            if ($detFact[$i]['I_M_IVA'] == '1') {
                $val_iva12 = $val_iva12 + floatval($detFact[$i]['VAL_IVA']);
                //$val_iva12 = $val_iva12 + (floatval($detFact[$i]['T_VENTA'])*$obj_var->valorIva);
                $vet_iva12 = $vet_iva12 + $valSinImp;
            } else {
                $val_iva0 = 0;
                $vet_iva0 = $vet_iva0 + $valSinImp;
            }
          
            $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDetalleNotaCredito
                    (CodigoPrincipal,CodigoAuxiliar,Descripcion,Cantidad,PrecioUnitario,Descuento,PrecioTotalSinImpuesto,IdNotaCredito) VALUES (
                    '" . $detFact[$i]['COD_ART'] . "', 
                    '1',
                    '" . $detFact[$i]['NOM_ART'] . "', 
                    '" . $detFact[$i]['CAN_DEV'] . "',
                    '" . $detFact[$i]['P_VENTA'] . "',
                    '" . $detFact[$i]['VAL_DES'] . "',
                    '$valSinImp',
                    '$idCab')";

            
            $command = $con->prepare($sql);
            $command->execute();
            $idDet = $con->insert_id;
            //Inserta el IVA de cada Item devuelto
            if ($detFact[$i]['I_M_IVA'] == '1') {//Verifico si el ITEM tiene Impuesto
                //Segun Datos Sri
                $this->InsertarDetImpNC($con,$obj_con, $idDet, '2', '2', '12', $valSinImp, $detFact[$i]['VAL_IVA']); //12%
            } else {//Caso Contrario no Genera Impuesto
                $this->InsertarDetImpNC($con,$obj_con, $idDet, '2', '0', '0', $valSinImp, $detFact[$i]['VAL_IVA']); //0%
            }
        }
        //Inserta el Total del Iva Acumulado en el detalle
        //Insertar Datos de Iva 0%
        If ($vet_iva0 > 0) {
            $this->InsertarNcImpuesto($con,$obj_con, $idCab, '2', '0', '0', $vet_iva0, $val_iva0);
        }
        //Inserta Datos de Iva 12
        If ($vet_iva12 > 0) {
            $this->InsertarNcImpuesto($con,$obj_con, $idCab, '2', '2', '12', $vet_iva12, $val_iva12);
        }
    }

    private function InsertarDetImpNC($con,$obj_con, $idDet, $codigo, $CodigoPor, $Tarifa, $t_venta, $val_iva) {
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDetalleNotaCreditoImpuesto 
                 (Codigo,CodigoPorcentaje,BaseImponible,Tarifa,Valor,IdDetalleNotaCredito)VALUES(
                 '$codigo','$CodigoPor','$t_venta','$Tarifa','$val_iva','$idDet')";
        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);
    }

    private function InsertarNcImpuesto($con,$obj_con, $idCab, $codigo, $CodigoPor, $Tarifa, $t_venta, $val_iva) {
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeNotaCreditoImpuesto 
                 (Codigo,CodigoPorcentaje,BaseImponible,Tarifa,Valor,IdNotaCredito)VALUES(
                 '$codigo','$CodigoPor','$t_venta','$Tarifa','$val_iva','$idCab')";
        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);
    }

    private function InsertarNcDatoAdicional($con,$obj_con, $i, $cabFact, $idCab) {
        $direccion = $cabFact[$i]['DIR_CLI'];
        $telefono = $cabFact[$i]['TEL_N01'];
        $correo = $cabFact[$i]['CORRE_E'];
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDatoAdicionalNotaCredito 
                 (Nombre,Descripcion,IdNotaCredito)
                 VALUES
                 ('Direccion','$direccion','$idCab'),('Telefono','$telefono','$idCab'),('Correo','$correo','$idCab')";
        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);
    }
    
    private function actualizaErpCabNc($cabFact) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        try {
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $numero = $cabFact[$i]['NUM_DEV'];
                $tipo = $cabFact[$i]['TIP_DEV'];
                $ids=$cabFact[$i]['ID_DOC'];//Contine el IDs del Tabla Autorizacion
                $sql = "UPDATE " . $obj_con->BdServidor . ".IG0060 SET ENV_DOC='$ids'
                        WHERE TIP_DEV='$tipo' AND NUM_DEV='$numero' AND IND_UPD='L'";
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
    
    
    /************************************************************/
    /*********CONFIGURACION PARA DATOS DE USUARIOS
    /************************************************************/
    
    private function buscarFacturasRAD() {
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            $conCont = $obj_con->conexionIntermedio();
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnv=$obj_var->limitEnv;
            
            $sql = "SELECT IdFactura,AutorizacionSRI,FechaAutorizacion,Ambiente,TipoEmision,RazonSocial,NombreComercial,
                    Ruc,ClaveAcceso,CodigoDocumento,Establecimiento,PuntoEmision,Secuencial,DireccionMatriz,
                    FechaEmision,DireccionEstablecimiento,ContribuyenteEspecial,ObligadoContabilidad,TipoIdentificacionComprador,
                    GuiaRemision,RazonSocialComprador,IdentificacionComprador,TotalSinImpuesto,TotalDescuento,Propina,
                    ImporteTotal,Moneda,'1','',Estado
                        FROM " . $obj_con->BdIntermedio . ".NubeFactura 
                    WHERE Estado=2 AND IdRad='0' AND FechaCarga>'$fechaIni' limit $limitEnv ";
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
    
    private function buscarDetFacturasRAD($Ids) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionIntermedio();
        $rawData = array();
        $sql = "SELECT * FROM " . $obj_con->BdIntermedio . ".NubeDetalleFactura WHERE IdFactura='$Ids' ";
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
    
    public function insertarFacturasRAD() {
        
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionVsRAd();
        try {
            $cabFact = $this->buscarFacturasRAD();
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $this->InsertarCabFacturaRAD($con,$obj_con,$cabFact, $i);
                $idCab = $con->insert_id;
                $detFact=$this->buscarDetFacturasRAD($cabFact[$i]['IdFactura']);
                $this->InsertarDetFacturaRAD($con,$obj_con,$detFact,$idCab);
                $this->InsertarFacturaImpuestoRAD($con,$obj_con, $idCab,$cabFact[$i]['IdFactura']);
                $this->InsertarFacturaDatoAdicionalRAD($con,$obj_con,$idCab,$cabFact[$i]['IdFactura']);
                $cabFact[$i]['ID_DOC']=$idCab;//Actualiza el IDs Documento Autorizacon SRI
            }
            $con->commit();
            $con->close();
            $this->actualizaRAD($cabFact);
            //echo "ERP Actualizado";
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
    
    private function InsertarCabFacturaRAD($con,$obj_con, $objEnt, $i) {
        $Iva=0;
        $Ice=0;
        $IdUsuarioCreador=1;//Numero de Cedula del CLiente
        $ArchivoXml='';
        $sql = "INSERT INTO " . $obj_con->BdRad . ".VSFactura
                    (AutorizacionSRI,FechaAutorizacion,Ambiente,TipoEmision,RazonSocial,NombreComercial,
                    Ruc,ClaveAcceso,CodDoc,Estab,PtoEmi,Secuencial,DirMatriz,FechaEmision,DirEstablecimiento,
                    ContribuyenteEspecial,ObligadoContabilidad,TipoIdentificacionComprador,GuiaRemision,RazonSocialComprador,
                    IdentificacionComprador,TotalSinImpuesto,TotalDescuento,Propina,Iva,Ice,ImporteTotal,Moneda,IdUsuarioCreador,
                    FechaCreacion,ArchivoXml,Estado)VALUES(
                    '" . $objEnt[$i]['AutorizacionSRI'] . "',
                    '" . $objEnt[$i]['FechaAutorizacion'] . "',
                    '" . $objEnt[$i]['Ambiente'] . "',
                    '" . $objEnt[$i]['TipoEmision'] . "',
                    '" . $objEnt[$i]['RazonSocial'] . "',
                    '" . $objEnt[$i]['NombreComercial'] . "',
                    '" . $objEnt[$i]['Ruc'] . "',
                    '" . $objEnt[$i]['ClaveAcceso'] . "',
                    '" . $objEnt[$i]['CodigoDocumento'] . "',
                    '" . $objEnt[$i]['Establecimiento'] . "',
                    '" . $objEnt[$i]['PuntoEmision'] . "',
                    '" . $objEnt[$i]['Secuencial'] . "',
                    '" . $objEnt[$i]['DireccionMatriz'] . "',
                    '" . $objEnt[$i]['FechaEmision'] . "',
                    '" . $objEnt[$i]['DireccionEstablecimiento'] . "',
                    '" . $objEnt[$i]['ContribuyenteEspecial'] . "',
                    '" . $objEnt[$i]['ObligadoContabilidad'] . "',
                    '" . $objEnt[$i]['TipoIdentificacionComprador'] . "',
                    '" . $objEnt[$i]['GuiaRemision'] . "',
                    '" . $objEnt[$i]['RazonSocialComprador'] . "',
                    '" . $objEnt[$i]['IdentificacionComprador'] . "',
                    '" . $objEnt[$i]['TotalSinImpuesto'] . "',
                    '" . $objEnt[$i]['TotalDescuento'] . "',
                    '" . $objEnt[$i]['Propina'] . "',
                    '" .$Iva. "','" .$Ice. "',
                    '" . $objEnt[$i]['ImporteTotal'] . "',
                    '" . $objEnt[$i]['Moneda'] . "',
                    '" . $IdUsuarioCreador . "',
                    CURRENT_TIMESTAMP(),
                    '" . $ArchivoXml . "',
                    '" . $objEnt[$i]['Estado'] . "');";        
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarDetFacturaRAD($con,$obj_con, $detFact, $idCab) {    
        for ($i = 0; $i < sizeof($detFact); $i++) {
            $sql = "INSERT INTO " . $obj_con->BdRad . ".VSDetalleFactura
                        (CodigoPrincipal,CodigoAuxiliar,Descripcion,Cantidad,Descuento,
                        PrecioUnitario,PrecioTotalSinImpuesto,IdFactura)VALUES(
                        '" . $detFact[$i]['CodigoPrincipal'] . "',
                        '" . $detFact[$i]['CodigoAuxiliar'] . "',
                        '" . $detFact[$i]['Descripcion'] . "',
                        '" . $detFact[$i]['Cantidad'] . "',
                        '" . $detFact[$i]['Descuento'] . "',
                        '" . $detFact[$i]['PrecioUnitario'] . "',
                        '" . $detFact[$i]['PrecioTotalSinImpuesto'] . "',
                        '$idCab')";

            $command = $con->prepare($sql);
            $command->execute();
            $idDet = $con->insert_id;
            $this->InsertarDetImpFacturaRAD($con,$obj_con, $idDet,$detFact[$i]['IdDetalleFactura']); 
        }
        
    }
    
    private function InsertarDetImpFacturaRAD($con,$obj_con, $idDet,$Ids) {        
        $sql = "INSERT INTO " . $obj_con->BdRad . ".VSDetalleFacturaImpuesto
                    (Codigo,CodigoPorcentaje,BaseImponible,Tarifa,Valor,IdDetalleFactura)
                SELECT Codigo,CodigoPorcentaje,BaseImponible,Tarifa,Valor,$idDet
                    FROM " . $obj_con->BdIntermedio . ".NubeDetalleFacturaImpuesto
                WHERE IdDetalleFactura='$Ids'";
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarFacturaImpuestoRAD($con,$obj_con, $idCab,$Ids) {        
        $sql = "INSERT INTO " . $obj_con->BdRad . ".VSFacturaImpuesto
                    (Codigo,CodigoPorcentaje,BaseImponible,Tarifa,Valor,IdFactura)
                    SELECT Codigo,CodigoPorcentaje,BaseImponible,Tarifa,Valor,'$idCab'
                            FROM " . $obj_con->BdIntermedio . ".NubeFacturaImpuesto
                    WHERE IdFactura='$Ids' ";
        
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarFacturaDatoAdicionalRAD($con,$obj_con,$idCab,$Ids) {
        $sql = "INSERT INTO " . $obj_con->BdRad . ".VSFacturaAdicionales
                    (Nombre,Descripcion,IdFactura)
                SELECT Nombre,Descripcion,'$idCab' FROM " . $obj_con->BdIntermedio . ".NubeDatoAdicionalFactura
                WHERE IdFactura='$Ids' "; 
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function actualizaRAD($cabFact) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionIntermedio();
        try {
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $ids=$cabFact[$i]['ID_DOC'];//Contine el IDs del Tabla Autorizacion
                $IdFactura=$cabFact[$i]['IdFactura'];
                $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET IdRad='$ids' WHERE IdFactura='$IdFactura';";
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
    
    /************************************************************/
    /*********CONFIGURACION PARA ENVIAR CORREOS
    /************************************************************/
    public function enviarMailDoc() {
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionVsRAd();
        $dataMail = new mailSystem;
        $dataMail->file_to_attach='/opt/SEADOC/AUTORIZADO/FACTURAS/';//Ruta de Facturas
        $rutaLink='http://www.docsea.utimpor.com';
        try {
            $cabDoc = $this->buscarMailFacturasRAD($con,$obj_var,$obj_con);//Consulta Documentos para Enviar
            //Se procede a preparar con loa correos para enviar.
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                $rowUser=$obj_var->buscarCedRuc($cabDoc[$i]['CedRuc']);//Verifico si Existe la Cedula o Ruc
                if($rowUser['status'] == 'OK'){
                    //Existe el Usuario y su Correo Listo para enviar
                    $row=$rowUser['data'];
                    $cabDoc[$i]['CorreoPer']=$row['CorreoPer'];
                    $cabDoc[$i]['Clave']='';//No genera Clave
                }else{
                    //No Existe y se crea uno nuevo
                    $rowUser=$obj_var->insertarUsuarioPersona($obj_con,$cabDoc,$i);
                    $row=$rowUser['data'];
                    $cabDoc[$i]['CorreoPer']=$row['CorreoPer'];
                    $cabDoc[$i]['Clave']=$row['Clave'];//Clave Generada
                }
            }
            //Envia l iformacion de Correos que ya se completo
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                if(strlen($cabDoc[$i]['CorreoPer'])>0){
                    //Envia Correo
                    //$htmlMail="Hola como estas 2";
                    include('mensaje.php');
                    $htmlMail=$mensaje;
                    //$htmlMail=file_get_contents('mensaje.php');
                    $dataMail->Subject='Ha Recibido un(a) Factura Nuevo(a)!!! ';
                    $dataMail->fileXML='FACTURA-'.$cabDoc[$i]["Documento"].'.xml';
                    $resulMail=$dataMail->enviarMail($htmlMail,$cabDoc,$obj_var);
                    if($resulMail["status"]=='OK'){
                        $cabDoc[$i]['Estado']=6;//Correo Envia
                    }else{
                        $cabDoc[$i]['Estado']=7;//Correo No enviado
                    }
                    
                }else{
                    //No envia Correo 
                    //Error COrreo no EXISTE
                    $cabDoc[$i]['Estado']=7;//Correo No enviado
                }
                
            }
            $con->close();
            $this->actualizaEnvioMailRAD($cabDoc);
            //echo "ERP Actualizado";
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
    
    private function buscarMailFacturasRAD($con,$obj_var,$obj_con) {
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnvMail=$obj_var->limitEnvMail;
            $sql = "SELECT IdFactura Ids,AutorizacionSRI,FechaAutorizacion,IdentificacionComprador CedRuc,RazonSocialComprador RazonSoc,
                    ImporteTotal Importe,CONCAT(Estab,'-',PtoEmi,'-',Secuencial) Documento
            FROM " . $obj_con->BdRad . ".VSFactura WHERE Estado=2 limit $limitEnvMail ";
            //echo $sql;
            $sentencia = $con->query($sql);
            if ($sentencia->num_rows > 0) {
                while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                    $rawData[] = $fila;
                }
            }
            //$conCont->close();
            return $rawData;
       
    }
    
    private function actualizaEnvioMailRAD($cabFact) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionVsRAd();
        try {
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $Estado=$cabFact[$i]['Estado'];//Contine el IDs del Tabla Autorizacion
                $Ids=$cabFact[$i]['Ids'];
                $sql = "UPDATE " . $obj_con->BdRad . ".VSFactura SET Estado='$Estado' WHERE IdFactura='$Ids';";
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

    
    
}
