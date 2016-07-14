<?php 
$cabDocPDF='<div>
    <table style="width:100%;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">Comprobante que se modifica</span>
                </td>
                <td>
                    <span>Factura '.$cabFact[0]["NumDocModificado"].'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Fecha Emisión (Comprobante a modificar) </span>
                </td>
                <td>
                    <span>'.date($obj_var->datebydefault,strtotime($cabFact[0]["FechaEmisionDocModificado"])).'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Razón de Modificación </span>
                </td>
                <td>
                    <span>'.$cabFact[0]["MotivoModificacion"].'</span>
                </td>
            </tr>
            
        </tbody>
    </table>
</div>';
?>