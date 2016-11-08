<?php
include('NubeFactura.php');//para HTTP
$FacObj = new NubeFactura();
//$res= $FacObj->insertarFacturas();
$res=$FacObj->enviarDocumentos();
$res= $FacObj->enviarMailDoc();
//phpinfo();

?>