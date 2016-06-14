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

$cabDocPDF = '<table class="tabDetalle" style="width:100mm" >
    <tbody>
        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL '. $obj_var->textIva .'</span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($BASEIVA12)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL 0% </span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($BASEIVA0)   ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL no objeto IVA </span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($NOOBJIVA)  ?></span>
            </td>
        </tr>

        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL EXENTO IVA </span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($EXENTOIVA)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>DESCUENTO </span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($cabFact["TotalDescuento"])  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>ICE</span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($ICE)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>IVA '. $obj_var->textIva .'</span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($VALORIVA12)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>IRBPNR </span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($IRBPNR)  ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>PROPINA </span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($cabFact["Propina"]) ?></span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>VALOR TOTAL</span>
            </td>
            <td class="marcoCel dataNumber">
                <span><?php echo Yii::app()->format->formatNumber($cabFact["ImporteTotal"])  ?></span>
            </td>
        </tr>

    </tbody>
</table>';
?>