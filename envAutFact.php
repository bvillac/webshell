<?php
include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
$res=$obj->enviarDocAutorizacion();//Solo Utimpor
//$res= $obj->insertarFacturas();
//$res=$obj->enviarDocRecepcion();

?>