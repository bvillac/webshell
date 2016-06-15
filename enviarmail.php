<?php
include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
//$res= $obj->insertarFacturas();
//Inserta los Registro BD RAD
//$res= $obj->insertarFacturasRAD();

$res= $obj->enviarMailDoc();

//phpinfo();

?>