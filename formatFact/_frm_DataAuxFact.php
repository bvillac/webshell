<?php
$cabDocPDF = '<div>
    <table style="width:100mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td class="titleDetalle">
                    <span class="titleLabel">Información Adicional</span>
                </td>
            </tr>';
            for ($ix = 0; $ix < sizeof($adiFact); $ix++) {
                if($adiFact[$ix]['Descripcion']<>''){
                $cabDocPDF .= '<tr>
                    <td>
                        <span class="titleLabel">'.$adiFact[$ix]['Nombre'].'</span>
                        <span>'.utf8_encode($adiFact[$ix]['Descripcion']) .'</span>
                    </td>
                </tr>';
                }
            }
        $cabDocPDF .= '</tbody>
    </table>
</div>';
?>