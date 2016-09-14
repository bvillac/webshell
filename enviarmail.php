<?php
include('NubeFactura.php');//para HTTP
$FacObj = new NubeFactura();
$res= $FacObj->enviarMailDoc();
//phpinfo();

?>