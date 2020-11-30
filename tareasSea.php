<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include('ShellSea.php');//para HTTP
$obj = new ShellSea();
//$res=$obj->actulizarProductos();
//$res=$obj->importacionLinea('TD');
//$res=$obj->importacionLinea('T4');
//$res=$obj->importacionLinea('TA');


///Conversiones
//$str = 'Y2FybG9zX2VucmlxdWVfY2FzdHJvX2VzcGFuYS5wMTI=';
//$str = 'P0000032757.p12';
$str = 'xavier_omar_ashby_solis.p12';
echo base64_decode($str);
//echo base64_encode($str);
//echo "hi";

?>