<?php
include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
$res= $obj->insertarFacturas();

//include('NubeRetencion.php');
//$obj = new NubeRetencion();
//$res= $obj->insertarDocumentosFactura(1,'');
//$res= $obj->insertarDocumentosPasivos(3,'0000003530');
//$res= $obj->insertarDocumentosFactura(1,'');
//$res= $obj->insertarDocumentosPasivos(2,'');

//Guia Remision
/*include('NubeGuiaRemision.php');
$obj = new NubeGuiaRemision();
$res= $obj->insertarDocumentosGuia(1,'0000001070');*/

//print_r($res);
//echo $res['NUM_NOF'];
//echo $res['RazonSocial'];

?>