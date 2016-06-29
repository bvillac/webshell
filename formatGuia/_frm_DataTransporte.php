<div>
    <table style="width:200mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Carrier Identification') ?></span>
                    <span><?php echo $cabDoc['IdentificacionTransportista'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Start date') ?></span>
                    <span><?php echo date(Yii::app()->params["datebydefault"],strtotime($cabDoc['FechaInicioTransporte'])) ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Social reason and last name') ?></span>
                    <span><?php echo $cabDoc['RazonSocialTransportista'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'End date') ?></span>
                    <span><?php echo date(Yii::app()->params["datebydefault"],strtotime($cabDoc['FechaFinTransporte'])) ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Starting point') ?></span>
                    <span><?php echo $cabDoc['DireccionPartida'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Plate') ?></span>
                    <span><?php echo $cabDoc['Placa'] ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>