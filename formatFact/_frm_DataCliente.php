<?php 
$cabDocPDF='<div>
    <table style="width:100%;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">Razón Social/Nombre y Apellido:</span>
                    <span>'.utf8_encode($cabFact[$i]["RazonSocialComprador"]).'</span>
                </td>
                <td>
                    <span class="titleLabel">Identificación:</span>
                    <span>'.$cabFact[$i]["TipoIdentificacionComprador"].'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Fecha Emisión:</span>
                    <span>'.date($obj_var->datebydefault,strtotime($cabFact[$i]["FechaEmision"])).'</span>
                </td>
                <td>
                    <span class="titleLabel">Guía de Remisión:</span>
                    <span>'.$cabFact[$i]["GuiaRemision"].'</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>';
?>