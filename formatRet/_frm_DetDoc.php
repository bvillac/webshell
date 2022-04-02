<?php
$cabDocPDF = '<table style="width:100%" class="tabDetalle">
    <tbody>
        <tr>
            <td class="marcoCel titleDetalle">
                <span>Comprobante</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Número</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Fecha Emisión</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Periodo Fiscal</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Base Imponible</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Impuesto</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Porcentaje Retención</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Valor Retenido</span>
            </td>
        </tr>';
        for ($filx = 0; $filx < sizeof($detDoc); $filx++) {
            $cabDocPDF .= '<tr>
                <td class="marcoCel campoDetalle titleDetalle">'; $cabDocPDF .= ($detDoc[$filx]['CodDocRetener']=='01')?'FACTURA':''; $cabDocPDF .= '</td>
                <td class="marcoCel titleDetalle">'.$detDoc[$filx]['NumDocRetener'].'</td>
                <td class="marcoCel titleDetalle">'. date($obj_var->datebydefault,strtotime($detDoc[$filx]['FechaEmisionDocRetener'])).'</td>
                <td class="marcoCel titleDetalle">'.$cabFact[0]['PeriodoFiscal'].'</td>    
                <td class="marcoCel dataNumber">'. number_format($detDoc[$filx]['BaseImponible'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</td>
                <td class="marcoCel campoDetalle">'; $cabDocPDF .=($detDoc[$filx]['Codigo']=='1')?'RENTA':(($detDoc[$filx]['Codigo']=='2')?'IVA':'ISD'); $cabDocPDF .= '</td>
                <td class="marcoCel dataNumber">'. number_format($detDoc[$filx]['PorcentajeRetener'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</td>
                <td class="marcoCel dataNumber">'. number_format($detDoc[$filx]['ValorRetenido'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</td>
            </tr>';
        }
    $cabDocPDF .= '</tbody>
</table>';
?>