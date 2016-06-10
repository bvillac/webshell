<?php
$cabDocPDF = '<div>
    <table style="width:100mm" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">R.U.C.:</span>
                    <span class="titleNum_Ruc">'. $cabDoc[$i]["Ruc"] .'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel titleDocumento">'.$cabDoc[$i]["NombreDocumento"] .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Nº</span>
                    <span class="titleNum_Ruc">'. $cabDoc[$i]["NumDocumento"] .'</span>
                </td>
              
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">NÚMERO DE AUTORIZACIÓN</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span >'.$cabDoc[$i]["AutorizacionSRI"] .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">FECHA Y HORA AUTORIZACIÓN</span>
                    <span>'. $cabDoc[$i]["FechaAutorizacion"] .'</span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">AMBIENTE</span>
                    <span>'. ($cabDoc[$i]["Ambiente"]=="1")?"PRUEBA":"PRODUCCIÓN" .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">EMISIÓN</span>
                    <span>'.($cabDoc[$i]["TipoEmision"]=="1")?"NORMAL":"INDISPONIBILIDAD DEL SISTEMA" .'</span>
                </td>
                
            </tr>
            <tr>
                <td >
                    <span class="titleLabel">CLAVE DE ACCESO</span>
                </td>
            </tr>
            <tr>
                <td>';
                    //echo CHtml::image(Yii::app()->params["seaBarra"] .$cabDoc["IdentificacionComprador"]. '.png', 'Utimpor', array('width' => '280px', 'height' => '20px')); 
                 $cabDocPDF .= '</td>
            </tr>
            <tr>
                <td>
                    <span>'. $cabDoc[$i]["ClaveAcceso"] .'</span>
                </td>
            </tr>
        </tbody>
        
    </table>
</div>';
?>