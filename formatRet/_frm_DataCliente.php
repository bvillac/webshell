<?php 
$cabDocPDF='<div>
    <table style="width:100%;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">Razón Social/Nombre y Apellido: </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($cabFact[0]["RazonSocialSujetoRetenido"])).'</span>
                </td>
                <td>
                    <span class="titleLabel">Identificación: </span>
                    <span>'.$cabFact[0]["IdentificacionSujetoRetenido"].'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Fecha Emisión: </span>
                    <span>'.date($obj_var->datebydefault,strtotime($cabFact[0]["FechaEmision"])).'</span>
                </td>
                <td>
                </td>
            </tr>
        </tbody>
    </table>
</div>';
?>