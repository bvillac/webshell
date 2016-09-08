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
                
            </tr>
            
        </tbody>
        
    </table>
</div>';
?>