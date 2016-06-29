<div>
    <?php if(sizeof($adiDoc)>0){ ?>
    <table style="width:100mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td class="titleDetalle">
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Additional Information') ?></span>
                </td>
            </tr>
            <?php
            for ($i = 0; $i < sizeof($adiDoc); $i++) {
                if($adiDoc[$i]['Descripcion']<>''){
                ?>
                <tr>
                    <td>
                        <span class="titleLabel"><?php echo $adiDoc[$i]['Nombre'] ?></span>
                        <span><?php echo $adiDoc[$i]['Descripcion'] ?></span>
                    </td>
                </tr>
            <?php 
                }
            } ?>


        </tbody>
    </table>
    <?php } ?>
</div>