<?php
include('NubeNotasCredito.php');//para HTTP
$obj = new NubeNotasCredito();
//$res= $obj->insertarDocumentosNC(1,'');
$res= $obj->enviarDocRecepcion();
//$res= $obj->enviarDocAutorizacion();
?>
<?php //phpinfo() ?>
