<?php
include('cls_Base.php');//para HTTP
include('cls_Global.php');//para HTTP
include('EMPRESA.php');//para HTTP
include('VSValidador.php');
include('VSClaveAcceso.php');
include('mailSystem.php');
include('REPORTES.php');
class NubeRetencion {
    private $tipoDoc='07';
    
    private function buscarRetenciones($op,$NumPed) {
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            $conCont = $obj_con->conexionServidor();
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnv=$obj_var->limitEnv;
            //$sql = "SELECT TIP_NOF,CONCAT(REPEAT('0',9-LENGTH(RIGHT(NUM_NOF,9))),RIGHT(NUM_NOF,9)) NUM_NOF,
            /*
             * HAY LIQUIDACIONES POR REEMBOLSO QUE NO TIENE UN DOCUMENTO FISICO DE RETENCION
             * Es por eso que en nuestra consulta si no tiene numero de retencion no va a ser agregada a las Retenciones Electronicas
             * es decir no se va a genera un registro en las tablas intermedias
             */
            
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
                    //Considear Reembolso AND A.NUM_RET<>0 para no ser selecionados ni insertados
                    $sql = "SELECT A.TIP_PED,A.NUM_PED,A.FEC_PED,A.COD_PRO,A.NOM_PRO,A.DIR_PRO,A.N_S_PRO,A.N_F_PRO,A.F_F_PRO,A.COD_SUS,
                                        A.COD_I_P,A.BAS_IV0,A.BAS_IVA,A.VAL_IVA,A.TIP_RET,A.NUM_RET,A.POR_RET,A.VAL_RET,A.TIP_RE1,A.BAS_RE1,
                                        A.POR_RE1,A.VAL_RE1,A.P_R_IVA,A.V_R_IVA,A.FEC_RET,A.DET_RET,B.CED_RUC,A.USUARIO,B.TEL_N01,B.CORRE_E,'' ID_DOC
                                     FROM " .  $obj_con->BdServidor . ".IG0054 A
                                             INNER JOIN " .  $obj_con->BdServidor . ".MG0032 B
                                                     ON B.COD_PRO=A.COD_PRO
                             WHERE A.TIP_PED='PP' AND A.IND_UPD='L' AND A.FEC_PED>='$fechaIni' AND A.NUM_RET<>0 AND ENV_DOC='0' LIMIT $limitEnv ";
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
        //DATOS DE RETENCION COMPRAS
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
        //DATOS RETENCION PROVICIONES
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
                //Por el 332, si no existe Numero de Retencion no se genera documento a validar Byron 24-08-2015 
                if(strlen($cabDoc[$i]['NUM_RET'])>0){
                    $this->InsertarCabRetencion($con,$obj_con,$cabDoc, $empresaEnt,$codDoc, $i);
                    $idCab = $con->insert_id;
                    $this->InsertarDetRetencion($con,$obj_con,$cabDoc,$idCab,$i,2);
                    $this->InsertarRetenDatoAdicional($con,$obj_con,$i,$cabDoc,$idCab);
                    $cabDoc[$i]['ID_DOC']=$idCab;//Actualiza el IDs Documento Autorizacon SRI
                }
               
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
        //INSERTA PROVICIONES DE PASIVOS
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
            $bas_imponible=($op==1)?$objEnt[$i]['BAS_IVA']:$objEnt[$i]['BAS_RET'];//Guarda el ls Base imponible dependiendo si es una compra o un provision
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
                ////Por el 332, si no existe Numero de Retencion no se genera documento a validar Byron 24-08-2015 
                if($ids>0){
                    $sql = "UPDATE " . $obj_con->BdServidor . ".IG0054 SET ENV_DOC='$ids'
                        WHERE TIP_PED='$tipo' AND NUM_PED='$numero' AND IND_UPD='L'";
                    //echo $sql;
                    $command = $conCont->prepare($sql);
                    $command->execute();
                }
                
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
        $objEmpData= new EMPRESA();
        $dataMail = new mailSystem();
        $rep = new REPORTES();
        //$con = $obj_con->conexionVsRAd();
        $objEmp=$objEmpData->buscarDataEmpresa($obj_var->emp_id,$obj_var->est_id,$obj_var->pemi_id);//recuperar info deL Contribuyente
        $con = $obj_con->conexionIntermedio();
     
        $dataMail->file_to_attachXML=$obj_var->rutaXML.'RETENCIONES/';//Rutas FACTURAS
        $dataMail->file_to_attachPDF=$obj_var->rutaPDF;//Ructa de Documentos PDF
        try {
            $cabDoc = $this->buscarMailRetenRAD($con,$obj_var,$obj_con);//Consulta Documentos para Enviar
            //Se procede a preparar con los correos para enviar.
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                //Retorna Informacion de Correos
                $rowUser=$obj_var->buscarCedRuc($cabDoc[$i]['CedRuc']);//Verifico si Existe la Cedula o Ruc
                if($rowUser['status'] == 'OK'){
                    //Existe el Usuario y su Correo Listo para enviar
                    $row=$rowUser['data'];
                    $cabDoc[$i]['CorreoPer']=$row['CorreoPer'];
                    $cabDoc[$i]['Clave']='';//No genera Clave
                }else{
                    //No Existe y se crea uno nuevo
                    $rowUser=$obj_var->insertarUsuarioPersona($obj_con,$cabDoc,'MG0032',$i);//Envia la Tabla de Dadtos de Person ERP
                    $row=$rowUser['data'];
                    $cabDoc[$i]['CorreoPer']=$row['CorreoPer'];
                    $cabDoc[$i]['Clave']=$row['Clave'];//Clave Generada
                }
            }
            //Envia l iformacion de Correos que ya se completo
            for ($i = 0; $i < sizeof($cabDoc); $i++) {
                if(strlen($cabDoc[$i]['CorreoPer'])>0){                
                    $mPDF1=$rep->crearBaseReport();
                    //Envia Correo                   
                    include('mensaje.php');
                    $htmlMail=$mensaje;

                    $dataMail->Subject='Ha Recibido un(a) Documento Nuevo(a)!!! ';
                    $dataMail->fileXML='COMPROBANTE DE RETENCION-'.$cabDoc[$i]["NumDocumento"].'.xml';
                    $dataMail->filePDF='COMPROBANTE DE RETENCION-'.$cabDoc[$i]["NumDocumento"].'.pdf';
                    //CREAR PDF
                    $mPDF1->SetTitle($dataMail->filePDF);
                    $cabFact = $this->mostrarCabRetencion($con,$obj_con,$cabDoc[$i]["Ids"]);
                    $detDoc = $this->mostrarDetRetencion($con,$obj_con,$cabDoc[$i]["Ids"]);
                    $adiDoc = $this->mostrarRetencionDataAdicional($con,$obj_con,$cabDoc[$i]["Ids"]);;
                    include('formatRet/retencionPDF.php');
                    $mPDF1->WriteHTML($mensajePDF); //hacemos un render partial a una vista preparada, en este caso es la vista docPDF
                    $mPDF1->Output($obj_var->rutaPDF.$dataMail->filePDF, 'F');//I en un naverdoad  F=ENVIA A UN ARCHVIO
                    
                    $usuData=$objEmpData->buscarDatoVendedor($cabFact[0]["USU_ID"]);
                    
                    $resulMail=$dataMail->enviarMail($htmlMail,$cabDoc,$obj_var,$usuData,$i);
                    if($resulMail["status"]=='OK'){
                        $cabDoc[$i]['EstadoEnv']=6;//Correo Envia
                    }else{
                        $cabDoc[$i]['EstadoEnv']=7;//Correo No enviado
                    }
                    
                }else{
                    //No envia Correo 
                    //Error COrreo no EXISTE
                    $cabDoc[$i]['EstadoEnv']=7;//Correo No enviado
                }
                
            }
            $con->close();
            $obj_var->actualizaEnvioMailRAD($cabDoc,"RT");
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
    
    
    private function buscarMailRetenRAD($con,$obj_var,$obj_con) {
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnvMail=$obj_var->limitEnvMail;
            $sql = "SELECT A.IdRetencion Ids,A.AutorizacionSRI,A.FechaAutorizacion,A.IdentificacionSujetoRetenido CedRuc,A.RazonSocialSujetoRetenido RazonSoc,
                    'COMPROBANTE DE RETENCION' NombreDocumento,A.Ruc,A.Ambiente,A.TipoEmision,A.EstadoEnv,
                    A.ClaveAcceso,CONCAT(A.Establecimiento,'-',A.PuntoEmision,'-',A.Secuencial) NumDocumento
                FROM " . $obj_con->BdIntermedio . ".NubeRetencion A "
                    . " WHERE A.Estado=2 AND A.EstadoEnv=2 AND A.FechaAutorizacion>='$fechaIni' limit $limitEnvMail ";             
            $sentencia = $con->query($sql);
            if ($sentencia->num_rows > 0) {
                while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                    $rawData[] = $fila;
                }
            }
            return $rawData;
       
    }
    
    private function mostrarCabRetencion($con,$obj_con,$id) {
        $rawData = array();
        $sql = "SELECT A.IdRetencion IdDoc,A.Estado,A.CodigoTransaccionERP,A.SecuencialERP,A.UsuarioCreador,
                    A.FechaAutorizacion,A.AutorizacionSRI,A.DireccionMatriz,A.DireccionEstablecimiento,
                    CONCAT(A.Establecimiento,'-',A.PuntoEmision,'-',A.Secuencial) NumDocumento,
                    A.ContribuyenteEspecial,A.ObligadoContabilidad,A.TipoIdentificacionSujetoRetenido,
                    A.CodigoDocumento,A.Establecimiento,A.PuntoEmision,A.Secuencial,A.PeriodoFiscal,
                    A.FechaEmision,A.IdentificacionSujetoRetenido,A.RazonSocialSujetoRetenido,
                    A.TotalRetencion,'COMPROBANTE DE RETENCION' NombreDocumento,A.ClaveAcceso,A.FechaAutorizacion,
                    A.Ambiente,A.TipoEmision,A.Ruc,A.CodigoError
                    FROM " . $obj_con->BdIntermedio . ".NubeRetencion A
                WHERE A.CodigoDocumento='$this->tipoDoc' AND A.IdRetencion =$id ";
        //echo $sql;
        $sentencia = $con->query($sql);
        if ($sentencia->num_rows > 0) {
            while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                $rawData[] = $fila;
            }
        }
        return $rawData;
    }

    private function mostrarDetRetencion($con,$obj_con,$id) {
        $rawData = array();
        $sql = "SELECT * FROM " . $obj_con->BdIntermedio . ".NubeDetalleRetencion WHERE IdRetencion=$id";
        //echo $sql;
        $sentencia = $con->query($sql);
        if ($sentencia->num_rows > 0) {
            //$rawData = $sentencia->fetch_assoc();
            while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                $rawData[] = $fila;
            }            
        }
        return $rawData;
    }


    private function mostrarRetencionDataAdicional($con,$obj_con,$id) {
        $rawData = array();
        $sql = "SELECT * FROM " . $obj_con->BdIntermedio . ".NubeDatoAdicionalRetencion WHERE IdRetencion=$id";
        $sentencia = $con->query($sql); 
        if ($sentencia->num_rows > 0) {
             while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                $rawData[] = $fila;
            }
        }
        return $rawData;
    }
    

}
