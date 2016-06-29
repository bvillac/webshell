<table style="width:200mm" class="tabDetalle">
    <tbody>
        <tr>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Quantity2') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Description') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Main code2') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Seconds code') ?></span>
            </td>
            
        </tr>
        <?php
        for ($i = 0; $i < sizeof($destDoc); $i++) {
            ?>
            <tr>
                <td class="marcoCel dataNumber"><?php echo intval($destDoc[$i]['Cantidad']) ?></td>
                <td class="marcoCel"><?php echo $destDoc[$i]['Descripcion'] ?></td>
                <td class="marcoCel campoAux"><?php echo $destDoc[$i]['CodigoInterno'] ?></td>
                <td class="marcoCel campoAux"><?php echo $destDoc[$i]['CodigoAdicional'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>