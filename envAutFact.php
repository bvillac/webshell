<?php
include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
//$res= $obj->insertarFacturas();
//$res=$obj->enviarDocRecepcion();
$res=$obj->enviarDocAutorizacion();
?>