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
    var $dateStartFact='2016-06-15';//'2015-03-20';//2015-07-20
    var $datebydefault='d-m-Y';
    var $decimalPDF=2;
    var $SepdecimalPDF='.';
    var $limitEnv=10;
    var $limitEnvMail=2;
    //var $valorIva=0.12;
    var $textIva='14%';
    var $Author="Utimpor";
    var $rutaPDF="/opt/SEADOC/DOCPDF/";
    var $rutaXML="/opt/SEADOC/AUTORIZADO/";
    var $rutaLink='http://www.docsea.utimpor.com';
    var $tipoFacLocal='F4';
    var $tipoGuiLocal='GR';
    
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
    
    public function insertarUsuarioPersona($obj_con,$cabDoc,$i) {  
        //$obj_con = new cls_Base();
        $conUse = $obj_con->conexionAppWeb();
        try {
            $this->InsertarPersona($conUse,$cabDoc,$obj_con,$i);
            $IdPer = $conUse->insert_id;
            $keyUser=$this->InsertarUsuario($conUse, $cabDoc,$obj_con, $IdPer,$i);
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
    
    private function InsertarUsuario($con, $objEnt,$obj_con, $IdPer,$i) {
        $usuNombre = $objEnt[$i]['CedRuc'];
        $RazonSoc = $objEnt[$i]['RazonSoc'];
        $correo = ($objEnt[$i]['CorreoPer']<>'')?$objEnt[$i]['CorreoPer']:$this->buscarCorreoERP($obj_con,$usuNombre,'MG0031');//Consulta Tabla Clientes
        $pass = $objEnt[$i]['CedRuc'];//$this->generarCodigoKey(8);
        $sql = "INSERT INTO " . $obj_con->BdAppweb . ".USUARIO
                (PER_ID,USU_NOMBRE,USU_ALIAS,USU_CORREO,USU_PASSWORD,USU_EST_LOG,USU_FEC_CRE)VALUES
                ($IdPer,'$usuNombre','$RazonSoc','$correo',MD5('$pass'),'1',CURRENT_TIMESTAMP()) ";
        $command = $con->prepare($sql);
        $command->execute();
        //Retorna el Pass y el Correo Guardado
        $arroout["Clave"] = $pass;
        $arroout["CorreoPer"] = $correo;
        return $arroout;
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
                    Case "FA":
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET EstadoEnv='$Estado' WHERE IdFactura='$Ids';";
                        break;
                    Case "GR":
                        $sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeGuiaRemision SET EstadoEnv='$Estado' WHERE IdGuiaRemision='$Ids';";
                        break;
                    Case "RT":
                        //$sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET EstadoEnv='$Estado' WHERE IdFactura='$Ids';";
                        break;
                    Case "NC":
                        //$sql = "UPDATE " . $obj_con->BdIntermedio . ".NubeFactura SET EstadoEnv='$Estado' WHERE IdFactura='$Ids';";
                        break;
                    Case "ND":
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
    

}
