<?php
$cabDocPDF = '<table style="width:200mm" class="tabDetalle">
    <tbody>
        <tr>
            <td class="marcoCel titleDetalle">
                <span>Cod. Principal</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Cod.Auxiliar</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Cant.</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Descripción</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Detalle Adicional</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Detalle Adicional N2</span>
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
        </tr>
        <?php
        for ($i = 0; $i < sizeof($detFact); $i++) {
            ?>
            <tr>
                <td class="marcoCel">'.$detFact[$i]['CodigoPrincipal'] .'</td>
                <td class="marcoCel">'.$detFact[$i]['CodigoAuxiliar'] .'</td>
                <td class="marcoCel dataNumber">'.intval($detFact[$i]['Cantidad']).'</td>
                <td class="marcoCel">'.$detFact[$i]['Descripcion'] .'</td>
                <td class="marcoCel"></td>
                <td class="marcoCel"></td>                
                <td class="marcoCel dataNumber">'.$detFact[$i]['PrecioUnitario'] .'</td>
                <td class="marcoCel dataNumber">'. number_format($detFact[$i]['Descuento'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '')  .'</td>
                <td class="marcoCel dataNumber">'. number_format($detFact[$i]['PrecioTotalSinImpuesto'], $obj_var->decimalPDF, $obj_var->SepdecimalPDF, '') .'</td>
            </tr>
        <?php } ?>
    </tbody>
</table>';
?>