<?php 
$mensajePDF='
<html>
    <head>
        <style>
            body
            {
                width:100%;
                font-family:Arial;
                font-size:7pt;
                margin:0;
                padding:0;
            }
            .marcoDiv{
                border: 1px solid #165480;
                padding: 2mm;
            }
            .marcoCel{
                border: 1px solid #165480;
                padding: 1mm;

            }
            .dataNumber{
                text-align: right;

            }
            .titleDetalle{
                text-align: center;

            }
            .tabDetalle{
                border-spacing:0;
                border-collapse: collapse;
            }
            .titleLabel{
                font-size:7pt;
                /*color:#000;*/
                font-weight: bold ;
            }
            .titleRazon{
                font-size:10pt;
                /*color:#000;*/
                font-weight: bold ;
            }
            .titleDocumento{
                font-size:10pt;
                letter-spacing: 5px; 
            }
            .titleNum_Ruc{
                font-size:9pt;
            }
            

        </style>
    </head>
    <body>';
        $contador = count($cabDoc);
        if ($cabDoc !== null) {
            //echo $this->renderPartial('_barcode', array('cabFact' => $cabFact)); 
            require_once('barcode.inc.php'); 
            $code_number = $cabFact[0]["ClaveAcceso"];//Generamos Clave de Acceso
            //opcion 1 permite Guardar en una rutas y 0 Presenta por apantalla,   false=no muestro los numeros abajo.
            new barCodeGenrator($code_number,1,$obj_var->rutaPDF.$cabDoc[$i]['CedRuc'].'.png', 280, 150, false);
            $mensajePDF .= '<table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:50%;vertical-align: central" align="center">
                            <img src="logoPDF.png" style="width:250px;height:110px;">
                        </td>
                        <td rowspan="2" style="width:50%">';
                            include("formatNc/_frm_CabDoc.php");//echo $this->renderPartial('_frm_CabFact', array('cabFact' => $cabFact));
                        $mensajePDF .= $cabDocPDF;
                        $mensajePDF .= '</td> 
                    </tr>
                    <tr>
                        <td style="width:50%;vertical-align: bottom">';
                        include("formatFact/_frm_DataEmpresa.php");//echo $this->renderPartial("_frm_DataEmpresa");
                        $mensajePDF .= $cabDocPDF;
                        $mensajePDF .= '</td>
                    </tr>
                </tbody>
            </table>
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:100%">';
                            include("formatNc/_frm_DataCliente.php");//echo $this->renderPartial('_frm_DataCliente', array('cabFact' => $cabFact));
                            $mensajePDF .= $cabDocPDF;
                            $mensajePDF .= '</td>
                    </tr>
                    <tr>
                        <td style="width:100%">';
                            include("formatNc/_frm_DataComprobante.php");//echo $this->renderPartial('_frm_DataComprobante', array('cabFact' => $cabFact));
                            $mensajePDF .= $cabDocPDF;
                            $mensajePDF .= '</td>
                    </tr>
                </tbody>
            </table>

            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:100%">';
                            include("formatNc/_frm_DetDoc.php");//echo $this->renderPartial('_frm_DetFact', array('detFact' => $detFact));
                        $mensajePDF .= $cabDocPDF;
                        $mensajePDF .= '</td>
                    </tr>
                </tbody>
            </table>
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:70%;vertical-align: top">';
                            include("formatNc/_frm_DataAuxDoc.php");//echo $this->renderPartial('_frm_DataAuxFact', array('adiFact' => $adiFact));
                            $mensajePDF .= $cabDocPDF;
                            $mensajePDF .= '</td>
                        <td style="width:30%">';
                            include("formatNc/_frm_TotDoc.php");//echo $this->renderPartial('_frm_TotFact', array('impFact' => $impFact, 'cabFact' => $cabFact));
                            $mensajePDF .= $cabDocPDF;
                            $mensajePDF .= '</td>
                    </tr>
                </tbody>
            </table>';
        } 
    $mensajePDF .= '</body>
</html>';
?>