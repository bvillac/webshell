<?php
$cabDocPDF = '<table style="width:100%" class="tabDetalle">
    <tbody>
        <tr>
            <td class="marcoCel titleDetalle">
                <span>Cantidad</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Descripción</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Código Principal</span>
            </td>
            <td class="marcoCel titleDetalle">
                <span>Código Secundarío</span>
            </td>            
        </tr>';
        for ($filx = 0; $filx < sizeof($destDoc); $filx++) {
            $destaDoc=$destDoc[$filx]['GuiaDet'];
            for ($fil = 0; $fil < sizeof($destaDoc); $fil++) {
                $cabDocPDF .= '<tr>
                    <td class="marcoCel dataNumber">'.intval($destaDoc[$fil]['Cantidad']).'</td>
                    <td class="marcoCel">'. utf8_encode($obj_var->limpioCaracteresXML(trim($destaDoc[$fil]['Descripcion']))) .'</td>
                    <td class="marcoCel campoAux">'.$destaDoc[$fil]['CodigoInterno'].'</td>
                    <td class="marcoCel campoAux">'.$destaDoc[$fil]['CodigoAdicional'].'</td>
                </tr>';
            }
            
        }
    $cabDocPDF .= '</tbody>
</table>';
?>