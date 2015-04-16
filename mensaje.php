<?php $mensaje=' 
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            .titleLabel{
                /*font-size:7pt;*/
                color: blue;
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
                    <label class="titleLabel">Estimad@:</label><br>'.$cabDoc[$i]["RazonSoc"].'<br> 
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
                    <label class="titleLabel">Fecha Emisi&oacute;n :</label>
                    <span>'.$cabDoc[$i]["FechaAutorizacion"].'</span>
                </div>
            </div>
            

            <div class="trow">
                <div class="tcol-td form-group">
                    <p>
                        Adem&aacute;s puede realizar la impresi&oacute;n su documento accediendo a nuestro portal aqui.<br>
                        Atentamente,<br>
                        <label class="titleLabel">Utimpor S.A.</label>

                    </p>
                </div>
            </div>

        </div>
    </body>
</html>';
?>
