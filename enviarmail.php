<?php
//include('NubeFactura.php');//para HTTP
include('NubeGuiaRemision.php');

$GuiObj = new NubeGuiaRemision();
//$FacObj = new NubeFactura();

//$res= $obj->insertarFacturas();
//Inserta los Registro BD RAD
//$res= $obj->insertarFacturasRAD();

//$res= $FacObj->enviarMailDoc();
$res= $GuiObj->enviarMailDoc();

//phpinfo();

?>