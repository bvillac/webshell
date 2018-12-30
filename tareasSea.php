<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include('ShellSea.php');//para HTTP
$obj = new ShellSea();
$res=$obj->importacionLinea('TD');
$res=$obj->importacionLinea('T4');
$res=$obj->importacionLinea('TA');
?>