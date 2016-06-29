<!DOCTYPE html>
<html>
    <head>
        <style>
            body
            {
                width:100%;
                font-family:Arial;
                font-size:7pt;
                margin:0;
                padding:0;
            }
            .marcoDiv{
                border: 1px solid #165480;
                padding: 2mm;
            }
            .marcoCel{
                border: 1px solid #165480;
                padding: 1mm;

            }
            .campoDetalle{
                width:200px;

            }
            .campoAux{
                width:90px;

            }
            .dataNumber{
                text-align: right;
                width:30px;

            }
            .titleDetalle{
                text-align: center;
            }
            .tabDetalle{
                border-spacing:0;
                border-collapse: collapse;
            }
            .titleLabel{
                font-size:7pt;
                /*color:#000;*/
                font-weight: bold ;
            }
            .titleRazon{
                font-size:10pt;
                /*color:#000;*/
                font-weight: bold ;
            }
            .titleDocumento{
                font-size:10pt;
                letter-spacing: 3px; 
            }
            .titleNum_Ruc{
                font-size:9pt;
            }
        </style>
    </head>
    <body>
        <?php
        $contador = count($cabDoc);
        if ($cabDoc !== null) {
            ?>
            <?php echo $this->renderPartial('_barcode', array('ClaveAcceso' => $cabDoc['ClaveAcceso'],'Identificacion' => $cabDoc['IdentificacionSujetoRetenido'])); ?>
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:50%">
                            <?php echo CHtml::image(Yii::app()->theme->baseUrl . '/images/plantilla/logo.png', 'Utimpor', array('width' => '300px', 'height' => '50px')); ?>
                        </td>
                        <td rowspan="2" style="width:50%">
                            <?php echo $this->renderPartial('_frm_CabDoc', array('cabDoc' => $cabDoc)); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50%">
                            <?php echo $this->renderPartial('_frm_DataEmpresa'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td>
                            <?php echo $this->renderPartial('_frm_DataTransporte', array('cabDoc' => $cabDoc)); ?>
                        </td>
                    </tr>
                </tbody>
            </table>           

        <table style="width:100%;" class="marcoCel">
                <tbody>
                    <tr>
                        <td>
                            <?php echo $this->renderPartial('_frm_DataCliente', array('destDoc' => $destDoc)); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->renderPartial('_frm_DetDoc', array('destDoc' => $destDoc[0]['GuiaDet'])); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="width:100%">
                <tbody>
                    <tr>
                        <td style="width:70%">
                            <?php echo $this->renderPartial('_frm_DataAuxDoc', array('adiDoc' => $adiDoc)); ?>
                        </td>
                        
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </body>
</html>