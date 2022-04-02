<?php 
$cabDocPDF='<div>
    <table style="width:100%;" >
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">Comprobante de Venta: </span>
                    <span>'.$destDoc[0]['NumDocSustento'].'</span>
                </td>
                <td>
                    <span class="titleLabel">Fecha Emisión: </span>
                    <span>'; $cabDocPDF .= ($destDoc[0]["FechaEmisionDocSustento"]<>"0000-00-00")? date($obj_var->datebydefault,strtotime($destDoc[0]["FechaEmisionDocSustento"])):""; $cabDocPDF .= '</span>
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Número de Autorización: </span>
                    <span>'.$destDoc[0]['NumAutDocSustento'].'</span>
                </td>
                <td>
                    <span class="titleLabel">Documento Aduanero: </span>
                    <span>'.$destDoc[0]['DocAduaneroUnico'].'</span>
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Motivo Traslado: </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($destDoc[0]['MotivoTraslado'])).'</span>
                </td>
                <td>
                    <span class="titleLabel">Código Establecimiento Destino: </span>
                    <span>'.$destDoc[0]['CodEstabDestino'].'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Destino (Punto llegada): </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($destDoc[0]['DirDestinatario'])).'</span>
                </td>
                <td>
                    <span class="titleLabel">Ruta: </span>
                    <span>'.$destDoc[0]['Ruta'].'</span>
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Identificación (Destinatario): </span>
                    <span>'.$destDoc[0]['IdentificacionDestinatario'].'</span>
                </td>
                <td>
                    
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Razón Social/Nombre y Apellido: </span>
                    <span>'.utf8_encode($obj_var->limpioCaracteresXML($destDoc[0]['RazonSocialDestinatario'])).'</span>
                </td>
                <td>
                    
                </td>
            </tr>
            
        </tbody>
    </table>
</div>';
?>