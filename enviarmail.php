<?php
include('NubeFactura.php');//para HTTP
$obj = new NubeFactura();
//$res= $obj->insertarFacturas();

//$res= $obj->insertarFacturasRAD();
$res= $obj->enviarMailDoc();

//Enviar E-MAIL a Usuarios
/*include('cls_Global.php');
include('mailSystem.php');
$dataMail = new mailSystem();
$obj_var = new cls_Global();
$htmlMail="Hola como estas";
echo $dataMail->enviarMail($htmlMail,'',$obj_var);*/

//phpinfo();

?>