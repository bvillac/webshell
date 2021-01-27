<?php
$cabDocPDF = '<div>
    <table style="width:100mm" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleRazon">'.strtoupper($objEmp["RazonSocial"]) .'</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Dir Matriz:</span>
                    <span>'.strtoupper($objEmp['DireccionMatriz']) .'</span>
                </td>
                
            </tr>';
//            <tr>
//                <td>
//                    <span class="titleLabel">Dir Sucursal:</span>
//                    <span>'.strtoupper($objEmp['DireccionSucursal']) .'</span>
//                </td>                
//            </tr>
//            <tr>
//                <td>
//                    <span class="titleLabel">Contribuyente Especial:</span>
//                    <span>'.strtoupper(($objEmp['ContribuyenteEspecial']!='')?$objEmp['ContribuyenteEspecial']:' NO') .'</span>
//                </td>
//            </tr>
$cabDocPDF .='<tr>
                <td>
                    <span class="titleLabel">OBLIGADO A LLEVAR CONTABILIDAD:</span>
                    <span>'.strtoupper($objEmp['ObligadoContabilidad']) .'</span>
                </td>                
            </tr>';
            if(var_dump(isset($objEmp['RegMicro']))){
                //devuelve fALSE si la variable esta declarada y tiene un valor null
                //y true si tieene un valor diferente de null
                if($objEmp['RegMicro']<>''){            
                    $cabDocPDF .= '<tr>
                        <td>
                            <span class="titleLabel">'.utf8_encode($objEmp['RegMicro']).'</span>             
                        </td>
                    </tr>';
                } 
            } 
            
            if($objEmp['AgenteRet']<>''){            
                $cabDocPDF .= '<tr>
                    <td>
                        <span class="titleLabel">Agente de Retención Resolución No. 00'.$objEmp['AgenteRet'].'</span>
                    </td>
                </tr>';
            }
$cabDocPDF .='</tbody>      
    </table>
</div>';
//<tr>
//                <td>                    
//                   <span>AGENTE DE RETENCION SEGÚN RESOLUCIÓN N° NAC-DNCRASC20-00001</span>
//                </td>                
//            </tr>
?>