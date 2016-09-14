<?php
$cabDocPDF = '<div>
    <table style="width:100mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td class="titleDetalle">
                    <span class="titleLabel">Información Adicional</span>
                </td>
            </tr>';
            for ($ix = 0; $ix < sizeof($adiFact); $ix++) {
                if($adiFact[$ix]['Descripcion']<>''){
                $cabDocPDF .= '<tr>
                    <td>
                        <span class="titleLabel">'.utf8_encode($adiFact[$ix]['Nombre']).'</span>
                        <span>'.utf8_encode($obj_var->limpioCaracteresXML($adiFact[$ix]['Descripcion'])) .'</span>
                    </td>
                </tr>';
                }
            }
        $cabDocPDF .= '</tbody>
    </table>
    <br>
    <table style="width:100mm;" class="tabDetalle">
        <tbody>
            <tr>
                <td class="marcoCel titleDetalle">
                    <span class="titleLabel">Forma de Pago</span>
                </td>
                <td class="marcoCel titleDetalle">
                    <span>Total</span>
                </td>
                <td class="marcoCel titleDetalle">
                    <span>Plazo</span>
                </td>
                <td class="marcoCel titleDetalle">
                    <span>Tiempo</span>
                </td>
            </tr>';
            for ($ix = 0; $ix < sizeof($pagFact); $ix++) {
                if($pagFact[$i]['FormaPago']<>''){
                    $cabDocPDF .= '<tr>
                        <td class="marcoCel">'.utf8_encode($obj_var->limpioCaracteresXML($pagFact[$ix]['FormaPago'])) .'</td>
                        <td class="marcoCel dataNumber">'. number_format($pagFact[$ix]['Total'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</td>
                        <td class="marcoCel dataNumber">'.utf8_encode($pagFact[$ix]['Plazo']).'</td>
                        <td class="marcoCel">'.utf8_encode($pagFact[$ix]['UnidadTiempo']).'</td>
                    </tr>';
                }
            }
        $cabDocPDF .= '</tbody>
    </table>
</div>';
?>