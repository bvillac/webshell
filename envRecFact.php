<?php
include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
$res=$obj->enviarDocRecepcion();//SOLO UTIMPOR
//$res= $obj->insertarFacturas();
//$res=$obj->enviarDocAutorizacion();

?>