<?php
include('cls_Base.php');//para HTTP
include('cls_Global.php');//para HTTP
include('EMPRESA.php');//para HTTP
include('VSValidador.php');
include('VSClaveAcceso.php');
class NubeRetencion {
    
    private function buscarRetenciones($op,$NumPed) {
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
                    $sql = "SELECT A.TIP_PED,A.NUM_PED,A.FEC_PED,A.COD_PRO,A.NOM_PRO,A.DIR_PRO,A.N_S_PRO,A.N_F_PRO,A.F_F_PRO,A.COD_SUS,
                                        A.COD_I_P,A.BAS_IV0,A.BAS_IVA,A.VAL_IVA,A.TIP_RET,A.NUM_RET,A.POR_RET,A.VAL_RET,A.P_R_IVA,A.V_R_IVA,
                                        A.FEC_RET,A.DET_RET,B.CED_RUC,A.USUARIO,B.TEL_N01,B.CORRE_E,'' ID_DOC
                                     FROM " .  $obj_con->BdServidor . ".IG0050 A
                                             INNER JOIN " .  $obj_con->BdServidor . ".MG0032 B
                                                     ON B.COD_PRO=A.COD_PRO
                             WHERE A.TIP_PED='CO' AND A.IND_UPD='L' AND A.FEC_PED>='$fechaIni' AND ENV_DOC='0' LIMIT $limitEnv";
                    break;
                Case 2://Compras provisiones de pasivos
                    $sql = "SELECT A.TIP_PED,A.NUM_PED,A.FEC_PED,A.COD_PRO,A.NOM_PRO,A.DIR_PRO,A.N_S_PRO,A.N_F_PRO,A.F_F_PRO,A.COD_SUS,
                                        A.COD_I_P,A.BAS_IV0,A.BAS_IVA,A.VAL_IVA,A.TIP_RET,A.NUM_RET,A.POR_RET,A.VAL_RET,A.TIP_RE1,A.BAS_RE1,
                                        A.POR_RE1,A.VAL_RE1,A.P_R_IVA,A.V_R_IVA,A.FEC_RET,A.DET_RET,B.CED_RUC,A.USUARIO,B.TEL_N01,B.CORRE_E,'' ID_DOC
                                     FROM " .  $obj_con->BdServidor . ".IG0054 A
                                             INNER JOIN " .  $obj_con->BdServidor . ".MG0032 B
                                                     ON B.COD_PRO=A.COD_PRO
                             WHERE A.TIP_PED='PP' AND A.IND_UPD='L' AND A.FEC_PED>='$fechaIni' AND ENV_DOC='0' LIMIT $limitEnv ";
                    break;
                Case 3://Compras provisiones de pasivos por NUmero
                    $sql = "SELECT A.TIP_PED,A.NUM_PED,A.FEC_PED,A.COD_PRO,A.NOM_PRO,A.DIR_PRO,A.N_S_PRO,A.N_F_PRO,A.F_F_PRO,A.COD_SUS,
                                        A.COD_I_P,A.BAS_IV0,A.BAS_IVA,A.VAL_IVA,A.TIP_RET,A.NUM_RET,A.POR_RET,A.VAL_RET,A.TIP_RE1,A.BAS_RE1,
                                        A.POR_RE1,A.VAL_RE1,A.P_R_IVA,A.V_R_IVA,A.FEC_RET,A.DET_RET,B.CED_RUC,A.USUARIO,B.TEL_N01,B.CORRE_E,'' ID_DOC
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

    public function insertarDocumentosFactura($op,$NumPed) {
        
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionIntermedio();
        $objEmpData= new EMPRESA();
        /****VARIBLES DE SESION*******/
        $emp_id=$obj_var->emp_id;
        $est_id=$obj_var->est_id;
        $pemi_id=$obj_var->pemi_id;
        try {
            $cabDoc = $this->buscarRetenciones($op,$NumPed);//Compras inventarios
            $empresaEnt=$objEmpData->buscarDataEmpresa($emp_id,$est_id,$pemi_id);//recuperar info deL Contribuyente
            $codDoc='07';//Comprobante de Retencion
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                $this->InsertarCabRetencion($con,$obj_con,$cabDoc, $empresaEnt,$codDoc, $i);
                $idCab = $con->insert_id;
                $this->InsertarDetRetencion($con,$obj_con,$cabDoc,$idCab,$i,1);
                $this->InsertarRetenDatoAdicional($con,$obj_con,$i,$cabDoc,$idCab);
                $cabDoc[$i]['ID_DOC']=$idCab;//Actualiza el IDs Documento Autorizacon SRI
            }
            $con->commit();
            $con->close();
            $this->actualizaErpCabCompras($cabDoc);
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
    
    public function insertarDocumentosPasivos($op,$NumPed) {
        
        $obj_con = new cls_Base();
        $obj_var = new cls_Global();
        $con = $obj_con->conexionIntermedio();
        $objEmpData= new EMPRESA();
        /****VARIBLES DE SESION*******/
        $emp_id=$obj_var->emp_id;
        $est_id=$obj_var->est_id;
        $pemi_id=$obj_var->pemi_id;
        try {
            $cabDoc = $this->buscarRetenciones($op,$NumPed);//Provision de Pasivos
            $empresaEnt=$objEmpData->buscarDataEmpresa($emp_id,$est_id,$pemi_id);//recuperar info deL Contribuyente
            $codDoc='07';//Comprobante de Retencion
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                $this->InsertarCabRetencion($con,$obj_con,$cabDoc, $empresaEnt,$codDoc, $i);
                $idCab = $con->insert_id;
                $this->InsertarDetRetencion($con,$obj_con,$cabDoc,$idCab,$i,2);
                $this->InsertarRetenDatoAdicional($con,$obj_con,$i,$cabDoc,$idCab);
                $cabDoc[$i]['ID_DOC']=$idCab;//Actualiza el IDs Documento Autorizacon SRI
            }
            $con->commit();
            $con->close();
            $this->actualizaErpCabProvision($cabDoc);
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
    
    private function InsertarCabRetencion($con,$obj_con, $objEnt, $objEmp, $codDoc, $i) {
        $valida = new VSValidador();
        $tip_iden = $valida->tipoIdent($objEnt[$i]['CED_RUC']);
        $Secuencial = $valida->ajusteNumDoc($objEnt[$i]['NUM_RET'], 9);
        $ced_ruc = ($tip_iden == '07') ? '9999999999999' : $objEnt[$i]['CED_RUC'];
        /* Datos para Genera Clave */
        //$tip_doc,$fec_doc,$ruc,$ambiente,$serie,$numDoc,$tipoemision
        $objCla = new VSClaveAcceso();
        $serie = $objEmp['Establecimiento'] . $objEmp['PuntoEmision'];
        $fec_doc = date("Y-m-d", strtotime($objEnt[$i]['FEC_RET']));
        $perFiscal = date("m/Y", strtotime($objEnt[$i]['FEC_RET']));
        $ClaveAcceso = $objCla->claveAcceso($codDoc, $fec_doc, $objEmp['Ruc'], $objEmp['Ambiente'], $serie, $Secuencial, $objEmp['TipoEmision']);
        /** ********************** */
        $razonSocialDoc=str_replace("'","`",$objEnt[$i]['NOM_PRO']);// Error del ' en el Text se lo Reemplaza `
        //$nomCliente=$objEnt[$i]['NOM_PRO'];// Error del ' en el Text
       
        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeRetencion
                (Ambiente,TipoEmision,RazonSocial,NombreComercial,Ruc,ClaveAcceso,CodigoDocumento,PuntoEmision,Establecimiento,
                 Secuencial,DireccionMatriz,FechaEmision,DireccionEstablecimiento,ContribuyenteEspecial,ObligadoContabilidad,
                 TipoIdentificacionSujetoRetenido,IdentificacionSujetoRetenido,RazonSocialSujetoRetenido,PeriodoFiscal,
                 TotalRetencion,UsuarioCreador,SecuencialERP,CodigoTransaccionERP,Estado,FechaCarga)VALUES(
                        '" . $objEmp['Ambiente'] . "',
                        '" . $objEmp['TipoEmision'] . "',
                        '" . $objEmp['RazonSocial'] . "',
                        '" . $objEmp['NombreComercial'] . "',
                        '" . $objEmp['Ruc'] . "',
                        '$ClaveAcceso',
                        '$codDoc',
                        '" . $objEmp['PuntoEmision'] . "',
                        '" . $objEmp['Establecimiento'] . "',
                        '$Secuencial',
                        '" . $objEmp['DireccionMatriz'] . "', 
                        '$fec_doc', 
                        '" . $objEmp['DireccionMatriz'] . "', 
                        '" . $objEmp['ContribuyenteEspecial'] . "', 
                        '" . $objEmp['ObligadoContabilidad'] . "', 
                        '$tip_iden', 
                        '$ced_ruc', 
                        '$razonSocialDoc',
                        '$perFiscal',
                        0,
                        '" . $objEnt[$i]['USUARIO'] . "',                        
                        '$Secuencial', 
                        '" . $objEnt[$i]['TIP_PED'] . "-" . $objEnt[$i]['NUM_PED'] . "',
                        '1',CURRENT_TIMESTAMP() )";

        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);

    }
    
    
    private function InsertarDetRetencion($con,$obj_con, $objEnt, $idCab,$i,$op) {
        $valida = new VSValidador();
        $codigo=0;//codigo impuesto a retener
        $cod_retencion='';//Codigo de Porcentaje de Retencion
        $bas_imponible=0;//Base imponible para impuesto
        $por_retener=0;//Porcentaje de Retencion  
        $val_retenido=0;//Valor Retenido
        $codDocRet='01';//$objEnt[$i]['TIP_PED'];//Verificar  cuando Sea Otros Documento FACTURA='01'
        $n_s_pro=str_replace('-', '', $objEnt[$i]['N_S_PRO']);//Remplaza 001-001 ->001001
        $numDocRet = $n_s_pro.$valida->ajusteNumDoc($objEnt[$i]['N_F_PRO'], 9);
        $fecDocRet=$objEnt[$i]['F_F_PRO'];
        $TotalRetencion=0;
        $sqlRet="";

        if(strlen($objEnt[$i]['NUM_RET'])>0){
            //Valores Retención RENTA
            $codigo=1;
            $cod_retencion=trim($objEnt[$i]['TIP_RET']);//Tipo de Retencion de Fuente
            $bas_imponible=$objEnt[$i]['BAS_IVA'];
            $por_retener=$objEnt[$i]['POR_RET'];
            $val_retenido=$objEnt[$i]['VAL_RET'];
            $TotalRetencion=$TotalRetencion+$val_retenido;
            $sqlRet = "($codigo,'$cod_retencion',$bas_imponible,$por_retener,$val_retenido,'$codDocRet','$numDocRet','$fecDocRet',$idCab)";
            
            //Insertar Datos Retencion por Provision de Pasivos OP=2
            //Solo para casos donde existan mas de 2 retenciones
            if($op==2 && $objEnt[$i]['TIP_RE1']<>''){//Verifica si Hay Tipo de Retencion para Agregar Valores
                $codigo=1;
                $val_retenido=0;//Valor Retenido
                $cod_retencion=  trim($objEnt[$i]['TIP_RE1']);//Tipo de Retencion de Fuente
                $bas_imponible=$objEnt[$i]['BAS_RE1'];
                $por_retener=$objEnt[$i]['POR_RE1'];
                $val_retenido=$objEnt[$i]['VAL_RE1'];
                $TotalRetencion=$TotalRetencion+$val_retenido;
                $sqlRet .= ",($codigo,'$cod_retencion',$bas_imponible,$por_retener,$val_retenido,'$codDocRet','$numDocRet','$fecDocRet',$idCab)";
            }
            
            //Valores Retención IVA
            //Nota: Puede Existir retencion de fuent sin retenr iVA
            IF(floatval($objEnt[$i]['V_R_IVA'])>0){//Vierifica si hay valores de IVA
                $codigo=2;
                $cod_retencion=$this->numeroRIVA((int)$objEnt[$i]['P_R_IVA']);//Tipo de Retencion de Fuente
                $bas_imponible=$objEnt[$i]['VAL_IVA'];
                $por_retener=$objEnt[$i]['P_R_IVA'];
                $val_retenido=$objEnt[$i]['V_R_IVA'];
                $TotalRetencion=$TotalRetencion+$val_retenido;
                $sqlRet .= ",($codigo,'$cod_retencion',$bas_imponible,$por_retener,$val_retenido,'$codDocRet','$numDocRet','$fecDocRet',$idCab)";
            }
            //Valores Retención ISD
            
        }
        
        $this->actualizaTotalCabRetencion($con,$obj_con,$idCab,$TotalRetencion);
        
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDetalleRetencion
                (Codigo,CodigoRetencion,BaseImponible,PorcentajeRetener,ValorRetenido,CodDocRetener,
                 NumDocRetener,FechaEmisionDocRetener,IdRetencion)VALUES ";
        /*
         * nOTA revissar poque aveces no ingresa al $sqlRet cuando deberia ingresar en todas
         */
        $sql .= $sqlRet; 
        $command = $con->prepare($sql);
        $command->execute();

 
    }
    
    Private Function numeroRIVA($porcentaje) {
        $numero =0;
        switch ($porcentaje) {
            Case 30:
                $numero = 1;
                break;
            Case 70:
                $numero = 2;
                break;
            Case 100:
                $numero = 3;
                break;
            default:
                $numero = 0;
        }
        Return $numero;
    }
    
     private function InsertarRetenDatoAdicional($con,$obj_con, $i, $cabFact, $idCab) {
        $direccion = $cabFact[$i]['DIR_PRO'];
        $telefono = $cabFact[$i]['TEL_N01'];
        $correo = $cabFact[$i]['CORRE_E'];
        $sql = "INSERT INTO " . $obj_con->BdIntermedio . ".NubeDatoAdicionalRetencion 
                 (Nombre,Descripcion,IdRetencion)
                 VALUES
                 ('Direccion','$direccion','$idCab'),('Telefono','$telefono','$idCab'),('Correo','$correo','$idCab')";
        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);
    }
    private function actualizaTotalCabRetencion($con,$obj_con, $idCab, $TotalRetencion) {
        $sql="UPDATE " . $obj_con->BdIntermedio . ".NubeRetencion SET TotalRetencion=$TotalRetencion WHERE IdRetencion=$idCab";
        $command = $con->prepare($sql);
        $command->execute();
        //$command = $con->query($sql);
    }
    
    private function actualizaErpCabCompras($cabFact) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        try {
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $numero = $cabFact[$i]['NUM_PED'];
                $tipo = $cabFact[$i]['TIP_PED'];
                $ids=$cabFact[$i]['ID_DOC'];//Contine el IDs del Tabla Autorizacion
                $sql = "UPDATE " . $obj_con->BdServidor . ".IG0050 SET ENV_DOC='$ids'
                        WHERE TIP_PED='$tipo' AND NUM_PED='$numero' AND IND_UPD='L'";
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
    
    private function actualizaErpCabProvision($cabFact) {
        $obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        try {
            for ($i = 0; $i < sizeof($cabFact); $i++) {
                $numero = $cabFact[$i]['NUM_PED'];
                $tipo = $cabFact[$i]['TIP_PED'];
                $ids=$cabFact[$i]['ID_DOC'];//Contine el IDs del Tabla Autorizacion
                $sql = "UPDATE " . $obj_con->BdServidor . ".IG0054 SET ENV_DOC='$ids'
                        WHERE TIP_PED='$tipo' AND NUM_PED='$numero' AND IND_UPD='L'";
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
