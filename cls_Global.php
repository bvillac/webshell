<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cls_Global
 *
 * @author root
 */
//include('cls_Base.php');//para HTTP
class cls_Global {
    //put your code here
    var $emp_id='1';//Empresa
    var $est_id='1';//Establecimiento
    var $pemi_id='1';//Punto Emision
    var $consumidorfinal='9999999999';
    var $dateStartFact='2016-08-01';//'2015-03-20';//2015-07-20
    var $datebydefault='d-m-Y';
    var $decimalPDF=2;
    var $SepdecimalPDF='.';
    var $limitEnv=10;
    var $limitEnvMail=2;
    var $IVAdefault=14;//Valor de Iva por Defecto en Textos
    var $Author="Utimpor";
    var $rutaPDF="/opt/SEADOC/DOCPDF/";
    var $rutaXML="/opt/SEADOC/AUTORIZADO/";
    var $rutaLink='http://docsea.utimpor.com';
    var $tipoFacLocal='F4';
    var $tipoGuiLocal='GR';
    var $tipoRetLocal='RT';
    var $tipoNcLocal='NC';
    var $tipoNdLocal='ND';
    
    public function messageSystem($status,$error,$op,$message,$data) {
        $arroout["status"] = $status;
        $arroout["error"] = $error;
        $arroout["message"] = $message;
        $arroout["data"] = $data;
        return $arroout;
    }
    
    public function buscarCedRuc($cedRuc) {
        try {
            $obj_con = new cls_Base();
            $conCont = $obj_con->conexionAppWeb();
            $rawData = array();
            $cedRuc=trim($cedRuc);            
            $sql = "SELECT A.PER_ID Ids,A.PER_NOMBRE RazonSocial,IFNULL(B.USU_CORREO,'') CorreoPer
                        FROM " . $obj_con->BdAppweb . ".PERSONA A
                                INNER JOIN " . $obj_con->BdAppweb . ".USUARIO B
                                        ON A.PER_ID=B.PER_ID AND B.USU_EST_LOG=1
                WHERE A.PER_CED_RUC='$cedRuc' AND A.PER_EST_LOG=1 ";
            //echo $sql;
            $sentencia = $conCont->query($sql);
            if ($sentencia->num_rows > 0) {
                //Retorna Solo 1 Registro Asociado
                $rawData=$this->messageSystem('OK',null,null,null, $sentencia->fetch_assoc());  
            }else{
                $rawData=$this->messageSystem('NO_OK',null,null,null,null);  
            }
            $conCont->close();
            return $rawData;
        } catch (Exception $e) {
            //echo $e;
            $conCont->close();
            return $this->messageSystem('NO_OK', $e->getMessage(), null, null, null);
        }
    }
    
    public function insertarUsuarioPersona($obj_con,$cabDoc,$DBTable,$i) {  
        //$obj_con = new cls_Base();
        $conUse = $obj_con->conexionAppWeb();
        try {
            $this->InsertarPersona($conUse,$cabDoc,$obj_con,$i);
            $IdPer = $conUse->insert_id;
            $keyUser=$this->InsertarUsuario($conUse, $cabDoc,$obj_con, $IdPer,$DBTable,$i);
            $conUse->commit();
            $conUse->close();
            return $this->messageSystem('OK', null, null, null, $keyUser);
        } catch (Exception $e) {
            $conUse->rollback();
            $conUse->close();
            //throw $e;
            return $this->messageSystem('NO_OK', $e->getMessage(), null, null, null);
        }   
    }
    private function InsertarPersona($con, $objEnt,$obj_con,$i) {
        $sql = "INSERT INTO " . $obj_con->BdAppweb . ".PERSONA
                (PER_CED_RUC,PER_NOMBRE,PER_GENERO,PER_EST_LOG,PER_FEC_CRE)VALUES
                ('" . $objEnt[$i]['CedRuc'] . "','" . $objEnt[$i]['RazonSoc'] . "','M','1',CURRENT_TIMESTAMP()) ";
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarUsuario($con, $objEnt,$obj_con, $IdPer,$DBTable,$i) {
        $usuNombre = $objEnt[$i]['CedRuc'];
        $RazonSoc = $objEnt[$i]['RazonSoc'];
        $correo = ($objEnt[$i]['CorreoPer']<>'')?$objEnt[$i]['CorreoPer']:$this->buscarCorreoERP($obj_con,$usuNombre,$DBTable);//Consulta Tabla Clientes
        $pass =$this->generarCodigoKey(8);// $objEnt[$i]['CedRuc'];
        //Inserta Datos Tabla USUARIO
        $sql = "INSERT INTO " . $obj_con->BdAppweb . ".USUARIO
                (PER_ID,USU_NOMBRE,USU_ALIAS,USU_CORREO,USU_PASSWORD,USU_EST_LOG,USU_FEC_CRE)VALUES
                ($IdPer,'$usuNombre','$RazonSoc','$correo',MD5('$pass'),'1',CURRENT_TIMESTAMP()) ";
        $command = $con->prepare($sql);
        $command->execute();
        
        //Inserta Datas en la tabla USUARIO_EMPRESA con Su Rol
        $UsuId = $con->insert_id;
        $RolId = $this->retornaRolUser($DBTable);//Retorna el Rol segunta tabla Roles
        $sql = "INSERT INTO " . $obj_con->BdAppweb . ".USUARIO_EMPRESA
                (EMP_ID,USU_ID,ROL_ID,EST_LOG)VALUES
                ($this->emp_id,$UsuId,$RolId,1) ";
        $command = $con->prepare($sql);
        $command->execute();
        
        //Retorna el Pass y el Correo Guardado
        $arroout["Clave"] = $pass;
        $arroout["CorreoPer"] = $correo;
        return $arroout;
    }
    //Retrona ROL SEGUN TABLA ROLES
    private function retornaRolUser($tabla) {
        $IdsRol = 6; //ROL DE USER NORMAL POR DEFECTO
        switch ($tabla) {
            Case "MG0031":
                $IdsRol = 4; //CLIENTE
                break;
            Case "MG0032":
                $IdsRol = 5; //PROVEEDOR
                break;
            default:
                $IdsRol = 6; //USUARIO NORMAL
        }
        return $IdsRol;
    }

    //Genera un Codigo para Pass
    private function generarCodigoKey($longitud) {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern) - 1;
        for ($i = 0; $i < $longitud; $i++)
            $key .= $pattern{mt_rand(0, $max)};
        return $key;
    }
    //Consulta en la Tablas del ERP si existe un correo
    private function buscarCorreoERP($obj_con,$CedRuc, $DBTable) {
        //$obj_con = new cls_Base();
        $conCont = $obj_con->conexionServidor();
        $rawData='';
        $sql = "SELECT IFNULL(CORRE_E,'') CorreoPer  FROM " . $obj_con->BdServidor . ".$DBTable "
                . "WHERE CED_RUC='$CedRuc' AND CORRE_E<>'' ";
        //echo $sql;
        $sentencia = $conCont->query($sql);
        if ($sentencia->num_rows > 0) {
            $fila = $sentencia->fetch_assoc();
            $rawData= str_replace(",", ";", $fila["CorreoPer"]);//Remplaza las "," por el ";" Para poder enviar.
        }
        $conCont->close();
        return $rawData;
    }
    
    public function actualizaEnvioMailRAD($docDat,$tipDoc) {
        $obj_con = new cls_Base();
        //$conCont = $obj_con->conexionVsRAd();
        $conCont = $obj_con->conexionIntermedio();
        try {
            for ($i = 0; $i < sizeof($docDat); $i++) {
                $Estado=$docDat[$i]['EstadoEnv'];//Contine el IDs del Tabla Autorizacion
                $Ids=$docDat[$i]['Ids'];
                switch ($tipDoc) {
                    Case "FA"://FACTURAS
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET EstadoEnv='$Estado' WHERE IdFactura='$Ids';";
                        break;
                    Case "GR"://GUIAS DE REMISION
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeGuiaRemision SET EstadoEnv='$Estado' WHERE IdGuiaRemision='$Ids';";
                        break;
                    Case "RT"://RETENCIONES
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeRetencion SET EstadoEnv='$Estado' WHERE IdRetencion='$Ids';";
                        break;
                    Case "NC"://NOTAS DE CREDITO
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeNotaCredito SET EstadoEnv='$Estado' WHERE IdNotaCredito='$Ids';";
                        break;
                    Case "ND"://NOTAS DE DEBITO
                        //$sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET EstadoEnv='$Estado' WHERE IdFactura='$Ids';";
                        break;
                }
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
    
    public function limpioCaracteresXML($cadena) {
        $search = array("<", ">", "&", "'");
        $replace = array("&lt;", "&gt", "&amp;", "&apos");
        $final = str_replace($search, $replace, $cadena);
        return $final;
    }
    
    public function limpioCaracteresSQL($cadena) {
        $search = array("'");
        $replace = array("`");
        $final = str_replace($search, $replace, $cadena);
        return $final;
    }
    
    public static function retornaTarifaDelIva($tarifa) {
         //TABLA 18 FICHA TECNICA
        $codigo=0;
        switch (floatval($tarifa)) {
            Case 0:
                $codigo=0;
                break;
            Case 12:
                $codigo=2;
                break;
            Case 14:
                $codigo=3;
                break;
            Case 6:
                $codigo=6;//NO OBJETO DE IVA
                break;
            default:
                $codigo=7;//EXEPTO DE IVA
        }
        return $codigo;
     }
     
     public static function buscarDocAutorizacion($tipDoc) {
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            //$conCont = $obj_con->conexionServidor();
            $conCont = $obj_con->conexionIntermedio();
            $rawData = array();
            $fechaIni=$obj_var->dateStartFact;
            $limitEnv=$obj_var->limitEnv;
            switch ($tipDoc) {
                    Case "FA"://FACTURAS                        
                        $sql = "SELECT IdFactura Ids,ClaveAcceso,AutorizacionSri 
                                        FROM " . $obj_con->BdIntermedio . ".NubeFactura 
                                WHERE Estado=2 AND DATE(FechaCarga)>='$fechaIni' LIMIT $limitEnv ";                        
                        //$sql = "SELECT TIP_NOF TIPO,NUM_NOF NUMERO,ENV_DOC ID_DOC FROM " .  $obj_con->BdServidor . ".VC010101 
                        //        WHERE ClaveAcceso IS NULL AND AutorizacionSri IS NULL AND IND_UPD='L' AND FEC_VTA>'$fechaIni' LIMIT $limitEnv ";
                        break;
                    Case "GR"://GUIAS DE REMISION
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeGuiaRemision SET EstadoEnv='$Estado' WHERE IdGuiaRemision='$Ids';";
                        break;
                    Case "RT"://RETENCIONES
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeRetencion SET EstadoEnv='$Estado' WHERE IdRetencion='$Ids';";
                        break;
                    Case "NC"://NOTAS DE CREDITO
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeNotaCredito SET EstadoEnv='$Estado' WHERE IdNotaCredito='$Ids';";
                        break;
                    Case "ND"://NOTAS DE DEBITO
                        //$sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET EstadoEnv='$Estado' WHERE IdFactura='$Ids';";
                        break;
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
    

}
