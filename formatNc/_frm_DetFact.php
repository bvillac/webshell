
<table style="width:200mm" class="tabDetalle">
    <tbody>
        <tr>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Main code') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Main stub') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Quantity') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Description') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Additional details') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Additional details N2') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Unit price') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Discount') ?></span>
            </td>
            <td class="marcoCel titleDetalle">
                <span><?php echo Yii::t('DOCUMENTOS', 'Total price') ?></span>
            </td>
        </tr>
        <?php
        for ($i = 0; $i < sizeof($detFact); $i++) {
            ?>
            <tr>
                <td class="marcoCel"><?php echo $detFact[$i]['CodigoPrincipal'] ?></td>
                <td class="marcoCel"><?php echo $detFact[$i]['CodigoAuxiliar'] ?></td>
                <td class="marcoCel dataNumber"><?php echo intval($detFact[$i]['Cantidad']) ?></td>
                <td class="marcoCel"><?php echo $detFact[$i]['Descripcion'] ?></td>
                <td class="marcoCel"><?php //echo $detFact[$i]['CodigoPrincipal'] ?></td>
                <td class="marcoCel"><?php //echo $detFact[$i]['CodigoPrincipal'] ?></td>
                <!--<td class="marcoCel dataNumber"><?php //echo Yii::app()->format->formatNumber($detFact[$i]['PrecioUnitario']) ?></td>Problmas de Redondeo-->
                <td class="marcoCel dataNumber"><?php echo $detFact[$i]['PrecioUnitario'] ?></td>
                <td class="marcoCel dataNumber"><?php echo Yii::app()->format->formatNumber($detFact[$i]['Descuento']) ?></td>
                <td class="marcoCel dataNumber"><?php echo Yii::app()->format->formatNumber($detFact[$i]['PrecioTotalSinImpuesto'])  ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>