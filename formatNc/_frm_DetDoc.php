<?php
$cabDocPDF = '<table style="width:100%" class="tabDetalle">
    <tbody>
        <tr>
            <td class="marcoCel titleDetalle">
                <span>Cod. Principal</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Cantidad</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Descripción</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Precio Unitario</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Descuento</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Precio Total</span>
            </td>
        </tr>';
        for ($fil = 0; $fil < sizeof($detDoc); $fil++) {
            $cabDocPDF .= '<tr>
                <td class="marcoCel">'.$detDoc[$fil]['CodigoPrincipal'] .'</td>
                <td class="marcoCel dataNumber">'.intval($detDoc[$fil]['Cantidad']).'</td>
                <td class="marcoCel">'. utf8_encode($obj_var->limpioCaracteresXML(trim($detDoc[$fil]['Descripcion']))) .'</td>
                <td class="marcoCel dataNumber">'.$detDoc[$fil]['PrecioUnitario'] .'</td>
                <td class="marcoCel dataNumber">'. number_format($detDoc[$fil]['Descuento'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</td>
                <td class="marcoCel dataNumber">'. number_format($detDoc[$fil]['PrecioTotalSinImpuesto'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '') .'</td>
            </tr>';
        }
    $cabDocPDF .= '</tbody>
</table>';
?>