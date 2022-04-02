<?php
$cabDocPDF = '<div>';
if(sizeof($adiDoc)>0){
    $cabDocPDF .= '<table style="width:100mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td class="titleDetalle">
                    <span class="titleLabel">Información Adicional</span>
                </td>
            </tr>';
            for ($ix = 0; $ix < sizeof($adiDoc); $ix++) {
                if($adiDoc[$ix]['Descripcion']<>''){
                $cabDocPDF .= '<tr>
                    <td>
                        <span class="titleLabel">'.utf8_encode($adiDoc[$ix]['Nombre']).'</span>
                        <span>'.utf8_encode($obj_var->limpioCaracteresXML($adiDoc[$ix]['Descripcion'])) .'</span>
                    </td>
                </tr>';
                }
            }
        $cabDocPDF .= '</tbody>
    </table>';
 } 
 $cabDocPDF .= '</div>';
 ?>