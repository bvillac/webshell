<?php
/*include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
$res= $obj->insertarFacturas();*/

include('NubeRetencion.php');
$obj = new NubeRetencion();
$res= $obj->insertarDocumentos();

//print_r($res);
//echo $res['NUM_NOF'];
//echo $res['RazonSocial'];

?>