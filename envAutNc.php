<?php
include('NubeNotasCredito.php');//para HTTP
$obj = new NubeNotasCredito();
$res= $obj->enviarDocAutorizacion();
?>
