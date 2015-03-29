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
class cls_Global {
    //put your code here
    var $emp_id='1';//Empresa
    var $est_id='1';//
    var $pemi_id='1';//Punto Emision
    var $consumidorfinal='9999999999';
    var $dateStartFact='2015-02-27';
    var $limitEnv=5;
    var $limitEnvMail=5;
    
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
    
    public function insertarUsuarioPersona($cabDoc) {  
        $obj_con = new cls_Base();
        $con = $obj_con->conexionAppWeb();
        try {
            $this->InsertarPersona($con,$cabDoc,$obj_con);
            $idPer = $con->insert_id;
            $keyUser=$this->InsertarUsuario($con, $cabDoc,$obj_con, $IdPer);
            $con->commit();
            $con->close();
            return $this->messageSystem('OK', null, null, null, $keyUser);
        } catch (Exception $e) {
            $con->rollback();
            $con->close();
            //throw $e;
            return $this->messageSystem('NO_OK', $e->getMessage(), null, null, null);
        }   
    }
    private function InsertarPersona($con, $objEnt,$obj_con) {
        $sql = "INSERT INTO " . $obj_con->BdAppweb . ".PERSONA
                (PER_CED_RUC,PER_NOMBRE,PER_GENERO,PER_EST_LOG,PER_FEC_CRE)VALUES
                ('" . $objEnt['CedRuc'] . "','" . $objEnt['RazonSoc'] . "','M','1',CURRENT_TIMESTAMP()) ";
        $command = $con->prepare($sql);
        $command->execute();
    }
    
    private function InsertarUsuario($con, $objEnt,$obj_con, $IdPer) {
        $usuNombre = $objEnt['CedRuc'];
        $RazonSoc = $objEnt['RazonSoc'];
        $correo = '';//$objEnt['correo'];
        $pass = $this->generarCodigoKey(8);
        $sql = "INSERT INTO " . $obj_con->BdAppweb . ".USUARIO
                (PER_ID,USU_NOMBRE,USU_ALIAS,USU_CORREO,USU_PASSWORD,USU_EST_LOG,USU_FEC_CRE)VALUES
                ($IdPer,'$usuNombre','$RazonSoc','$correo',MD5('$pass'),'1',CURRENT_TIMESTAMP()) ";
        $command = $con->prepare($sql);
        $command->execute();
        
        $arroout["Clave"] = $pass;
        $arroout["CorreoPer"] = $correo;
        return $arroout;
    }
    
    private function generarCodigoKey($longitud) {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern) - 1;
        for ($i = 0; $i < $longitud; $i++)
            $key .= $pattern{mt_rand(0, $max)};
        return $key;
    }

}
