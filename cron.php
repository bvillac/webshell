<?php

include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
$res= $obj->insertarFacturas();
//print_r($res);
//echo $res['NUM_NOF'];
//echo $res['RazonSocial'];

?>