<?php
$IRBPNR=0;
$ICE=0;
for ($i = 0; $i < sizeof($impFact); $i++) {
    if ($impFact[$i]['Codigo'] == '2') {//Valores de IVA
        switch ($impFact[$i]['CodigoPorcentaje']) {
            case 0:
                $BASEIVA0=$impFact[$i]['BaseImponible'];
                break;
            case 2:
                $BASEIVA12=$impFact[$i]['BaseImponible'];
                $VALORIVA12=$impFact[$i]['Valor'];
                break;
            case 6://No objeto Iva
                $NOOBJIVA=$impFact[$i]['BaseImponible'];
                break;
            case 7://Excento de Iva
                $EXENTOIVA=$impFact[$i]['BaseImponible'];
                break;
            default:
        }
    }
}
?>
<table class="tabDetalle" style="width:100mm" >
    <tbody>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'SUBTOTAL 12%') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($BASEIVA12)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'SUBTOTAL 0%') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($BASEIVA0)   ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'SUBTOTAL IVA no object') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($NOOBJIVA)  ?></span>
            </td>
        </tr>
        <!--<tr>
            <td class="marcoCel">
                <span><?php //echo Yii::t('DOCUMENTOS', 'SUBTOTAL TAX FREE') ?></span>
            </td>
            <td class="marcoCel">
                <span><?php //echo $EXENTOIVA  ?></span>
            </td>
        </tr>-->
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'SUBTOTAL IVA EXEMPT') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($EXENTOIVA)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo strtoupper(Yii::t('DOCUMENTOS', 'Discount')) ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($cabFact['TotalDescuento'])  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'ICE') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($ICE)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'IVA 12%') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($VALORIVA12)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'IRBPNR') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($IRBPNR)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'GRATUITIES') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($cabFact['Propina']) ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span><?php echo Yii::t('DOCUMENTOS', 'TOTAL VALUE') ?></span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($cabFact['ValorModificacion'])  ?></span>
            </td>
        </tr>

    </tbody>
</table>