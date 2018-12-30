<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cls_Base
 *
 * @author root
 * Revisar Bloc para Utiliar Caracteres Especiales
 * http://xaviesteve.com/354/acentos-y-enes-aparecen-mal-a%C2%B1-en-php-con-mysql-utf-8-iso-8859-1/
 */
class cls_Base {
    var $BdServidor="utimpor2019"; 
    var $BdAppweb="APPWEB"; 
    var $BdIntermedio="VSSEAINTERMEDIA";
    var $BdRad="VSSEARAD"; 
    //SERVIDOR LOCAL APP SEA
    public function conexionServidor() {
        //Configuracion Local
        $bd_host = "192.168.10.1";
        //$bd_host = "192.168.1.3";
        //$bd_host = "localhost";
        $bd_usuario = "root";
        $bd_password = "root00";
        //$bd_password = "";
        $bd_base = $this->BdServidor;
        //$con = mysql_connect($bd_host, $bd_usuario, $bd_password) or die("Error en la conexión a MySql");
        //mysql_select_db($bd_base, $con);
        //Creando la conexión, nuevo objeto mysqli
        $con = new mysqli($bd_host,$bd_usuario,$bd_password,$bd_base);
        $con->set_charset('utf8');//Convierte todo lo que esté codificado de latin1 a UTF-8 Errore de Ñ o Caractes especiales        
         //Si sucede algún error la función muere e imprimir el error
        if($con->connect_error){
            die("Error en la conexion : ".$con->connect_errno."-".$con->connect_error);
        }
        //Si nada sucede retornamos la conexión
        return $con;
    }
    public function getBdServidor() {
        return $this->BdServidor;
    }
        
    //SERVIDOR REMOTO WEBAPP
    public function conexionIntermedio() {
        //Configuracion Local
        //$bd_host = "192.168.10.200";
        $bd_host = "localhost";
        $bd_usuario = "root";
        $bd_password = "root00";
        $bd_base = $this->BdIntermedio;
        //$con = mysql_connect($bd_host, $bd_usuario, $bd_password) or die("Error en la conexión a MySql");
        //mysql_select_db($bd_base, $con);
        //mysql_query("SET NAMES 'utf8'");
        $con = new mysqli($bd_host,$bd_usuario,$bd_password,$bd_base);
        $con->set_charset('utf8');//Convierte todo lo que esté codificado de latin1 a UTF-8 Errore de Ñ o Caractes especiales 
        if($con->connect_error){
            die("Error en la conexion : ".$con->connect_errno."-".$con->connect_error);
        }
        return $con;
    }
    public function getIntermedio() {
        return $this->BdIntermedio;
    }
    //SERVIDOR REMOTO WEBAPP
    public function conexionAppWeb() {
        //Configuracion Local
        //$bd_host = "192.168.10.200";
        $bd_host = "localhost";
        $bd_usuario = "root";
        $bd_password = "root00";
        $bd_base = $this->BdAppweb;
        //$con = mysql_connect($bd_host, $bd_usuario, $bd_password) or die("Error en la conexión a MySql");
        //mysql_select_db($bd_base, $con);
        $con = new mysqli($bd_host,$bd_usuario,$bd_password,$bd_base);
        $con->set_charset('utf8');//Convierte todo lo que esté codificado de latin1 a UTF-8 Errore de Ñ o Caractes especiales 
        if($con->connect_error){
            die("Error en la conexion : ".$con->connect_errno."-".$con->connect_error);
        }
        return $con;
    }
    public function getBdAppweb() {
        return $this->BdAppweb;
    }
    //SERVIDOR REMOTO VSSEARAD
    public function conexionVsRAd() {
        //Configuracion Local
        //$bd_host = "192.168.10.200";
        $bd_host = "localhost";
        $bd_usuario = "root";
        $bd_password = "root00";
        $bd_base = $this->BdRad;
        $con = new mysqli($bd_host,$bd_usuario,$bd_password,$bd_base);
        $con->set_charset('utf8');//Convierte todo lo que esté codificado de latin1 a UTF-8 Errore de Ñ o Caractes especiales 
        if($con->connect_error){
            die("Error en la conexion : ".$con->connect_errno."-".$con->connect_error);
        }
        return $con;
    }
    public function getBdVsRAd() {
        return $this->BdRad;
    }

}
