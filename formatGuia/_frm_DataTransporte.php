<?php 
$cabDocPDF='<div>
    <table style="width:100%;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">Identificación (Transportista): </span>
                    <span>'.$cabFact[0]["IdentificacionTransportista"].'</span>
                </td>
                <td>
                    <span class="titleLabel">Fecha Inicio Transporte: </span>
                    <span>'.date($obj_var->datebydefault,strtotime($cabFact[0]["FechaInicioTransporte"])).'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Razón Social/Nombre y Apellido: </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($cabFact[0]["RazonSocialTransportista"])) .'</span>
                </td>
                <td>
                    <span class="titleLabel">Fecha Fin Transporte:</span>
                    <span>'.date($obj_var->datebydefault,strtotime($cabFact[0]["FechaFinTransporte"])).'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Punto de partida: </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($cabFact[0]["DireccionPartida"])) .'</span>
                </td>
                <td>
                    <span class="titleLabel">Placa: </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($cabFact[0]["Placa"])) .'</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>';
?>