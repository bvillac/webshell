<?php
$IRBPNR=0;
$ICE=0;
for ($ib = 0; $ib < sizeof($impFact); $ib++) {
    if ($impFact[$ib]['Codigo'] == '2') {//Valores de IVA
        switch ($impFact[$ib]['CodigoPorcentaje']) {
            case 0:
                $BASEIVA0=$impFact[$ib]['BaseImponible'];
                break;
            case 2:
                $BASEIVA12=$impFact[$ib]['BaseImponible'];
                $VALORIVA12=$impFact[$ib]['Valor'];
                break;
            case 6://No objeto Iva
                $NOOBJIVA=$impFact[$ib]['BaseImponible'];
                break;
            case 7://Excento de Iva
                $EXENTOIVA=$impFact[$ib]['BaseImponible'];
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
                <span>'. number_format($BASEIVA12, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL 0% </span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($BASEIVA0, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL no objeto IVA </span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($NOOBJIVA, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>

        <tr>
            <td class="marcoCel">
                <span>SUBTOTAL EXENTO IVA </span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($EXENTOIVA, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>DESCUENTO </span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($cabFact[$i]["TotalDescuento"], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>ICE</span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($ICE, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>IVA '. $obj_var->textIva .'</span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($VALORIVA12, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>IRBPNR </span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($IRBPNR, $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>PROPINA </span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($cabFact[$i]["Propina"], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>
        <tr>
            <td class="marcoCel">
                <span>VALOR TOTAL</span>
            </td>
            <td class="marcoCel dataNumber">
                <span>'. number_format($cabFact[$i]["ImporteTotal"], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</span>
            </td>
        </tr>

    </tbody>
</table>';
?>