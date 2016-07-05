<?php
include('NubeFactura.php');//para HTTP
$FacObj = new NubeFactura();
$res= $FacObj->enviarMailDoc();

//$res= $obj->insertarFacturas();
//Inserta los Registro BD RAD
//$res= $obj->insertarFacturasRAD();

//phpinfo();

?>