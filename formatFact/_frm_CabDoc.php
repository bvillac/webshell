<?php
$cabDocPDF = '<div>
    <table style="width:100mm" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">R.U.C.:</span>
                    <span class="titleNum_Ruc">'. $cabFact[$i]["Ruc"] .'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel titleDocumento">'.$cabFact[$i]["NombreDocumento"] .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Nº</span>
                    <span class="titleNum_Ruc">'. $cabFact[$i]["NumDocumento"] .'</span>
                </td>
              
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">NÚMERO DE AUTORIZACIÓN</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span >'.$cabFact[$i]["AutorizacionSRI"] .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">FECHA Y HORA AUTORIZACIÓN</span>
                    <span>'. $cabFact[$i]["FechaAutorizacion"] .'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">AMBIENTE</span>
                    <span>'; $cabDocPDF .= ($cabFact[$i]["Ambiente"]=="1")? "PRUEBA":"PRODUCCIÓN"; $cabDocPDF .= '</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">EMISIÓN</span>
                    <span>'; $cabDocPDF .= ($cabFact[$i]["TipoEmision"]=="1")?"NORMAL":"INDISPONIBILIDAD DEL SISTEMA"; $cabDocPDF .= '</span>
                </td>
            </tr>
            <tr>
                <td >
                    <span class="titleLabel">CLAVE DE ACCESO</span>
                </td>
            </tr>
            <tr>
                <td style="width:50%">
                            <img src="'.$obj_var->rutaPDF.$cabFact[$i]["IdentificacionComprador"].'.png" style="width:280px;height:20px;">
                </td>
            </tr>
            <tr>
                <td>
                    <span>'. $cabFact[$i]["ClaveAcceso"] .'</span>
                </td>
            </tr>    
            
        </tbody>
        
    </table>
</div>';
?>