<?php
include('NubeNotasCredito.php');
$NcObj = new NubeNotasCredito();
$res= $NcObj->enviarMailDoc();
//phpinfo();
//ok.
?>