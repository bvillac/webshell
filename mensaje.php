<?php $mensaje='
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            .titleLabel{
                /*font-size:7pt;*/
                color:black;
                font-weight: bold ;
            }
            .titleName{
                font-size:12pt;
                color:black;
                font-weight: bold ;
            }
        </style>
    </head>
    <body>
        <?php
        // put your code here
        ?>
        <div id="div-table">
            <div class="trow">
                <p>
                    <label class="titleLabel">Estimad@:</label><br><span class="titleName">'.utf8_encode($cabDoc[$i]["RazonSoc"]).'</span><br> 
                    Ha recibido un documendo electronico de <label class="titleLabel">'.strtoupper($objEmp["RazonSocial"]) .'</label>
                </p>
            </div>
            <div class="trow">
                <div class="tcol-td form-group">
                    <label class="titleLabel">Documento Nº :</label>
                    <span>'.$cabDoc[$i]["NumDocumento"].'</span>
                </div>
            </div>
            <div class="trow">
                <div class="tcol-td form-group">
                    <label class="titleLabel">Autorizaci&oacute;n Nº :</label>
                    <span>'.$cabDoc[$i]["AutorizacionSRI"].'</span>
                </div>
            </div>
            <div class="trow">
                <div class="tcol-td form-group">
                    <label class="titleLabel">Fecha Emisi&oacute;n :</label>
                    <span>'.$cabDoc[$i]["FechaAutorizacion"].'</span>
                </div>
            </div>';
            if($cabDoc[$i]["Clave"]<>''){//Adjunta Clave en Caso de Ser un Usuario Nuevo
                $mensaje.='<div class="trow">
                                <div class="tcol-td form-group">
                                    <span>Usuario Nuevo</span><br> 
                                    <label class="titleLabel">Clave :</label>
                                    <span>'.$cabDoc[$i]["Clave"].'</span>
                                </div>
                            </div>';
            }
            //Datos de prueba
            /*$DataCorreos = explode(";",$cabDoc[$i]["CorreoPer"]);
            for ($icor = 0; $icor < count($DataCorreos); $icor++) {
                $mensaje.='<div class="trow">
                                <div class="tcol-td form-group">
                                    <span> Correo :'.trim($DataCorreos[$icor]).'- Para :'.trim($cabDoc[$i]["RazonSoc"]).'</span><br>
                                </div>
                            </div>';
            }*/
            //Adem&aacute;s puede realizar la impresi&oacute;n su documento accediendo a nuestro portal <a target="_blank" href="'.$obj_var->rutaLink.'">aqui</a>.<br>
            $mensaje.='<div class="trow">
                <div class="tcol-td form-group">
                    <p>
                        Adem&aacute;s puede realizar la impresi&oacute;n su documento accediendo a nuestro portal <a target="_blank" href="'.$obj_var->rutaLink.'">aqui</a>.<br>
                        Atentamente,<br>
                        <label class="titleLabel">'.strtoupper($objEmp["RazonSocial"]) .'</label>
                    </p>
                </div>
            </div>

        </div>
    </body>
</html>';
?>
