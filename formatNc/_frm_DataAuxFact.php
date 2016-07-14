<div>
    <table style="width:100mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td class="titleDetalle">
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Additional Information') ?></span>
                </td>
            </tr>
            <?php
            for ($i = 0; $i < sizeof($adiFact); $i++) {
                if($adiFact[$i]['Descripcion']<>''){
                ?>
                <tr>
                    <td>
                        <span class="titleLabel"><?php echo $adiFact[$i]['Nombre'] ?></span>
                        <span><?php echo $adiFact[$i]['Descripcion'] ?></span>
                    </td>
                </tr>
            <?php 
                }
            } ?>


        </tbody>
    </table>
</div>