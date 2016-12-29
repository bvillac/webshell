<?php
include('NubeRetencion.php');
$obj = new NubeRetencion();
$res= $obj->insertarDocumentosFactura(1,'');//Compras alimenta inventarios
$res= $obj->insertarDocumentosPasivos(2,'');//Compras PROVICIONES
?>