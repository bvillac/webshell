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
                    <label class="titleLabel">Estimad@:</label><br><span class="titleName">'.$cabDoc[$i]["RazonSoc"].'</span><br> 
                    Ha recibido un documendo electronico de UTIMPOR S.A.
                </p>
            </div>
            <div class="trow">
                <div class="tcol-td form-group">
                    <label class="titleLabel">Documento Nº :</label>
                    <span>'.$cabDoc[$i]["Documento"].'</span>
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
            $mensaje.='<div class="trow">
                <div class="tcol-td form-group">
                    <p>
                        Adem&aacute;s puede realizar la impresi&oacute;n su documento accediendo a nuestro portal <a target="_blank" href="'.$rutaLink.'">aqui</a>.<br>
                        Atentamente,<br>
                        <label class="titleLabel">Utimpor S.A.</label>

                    </p>
                </div>
            </div>

        </div>
    </body>
</html>';
?>
