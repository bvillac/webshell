<div>
    <table style="width:100mm" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel">R.U.C.:</span>
                    <span class="titleNum_Ruc"><?php echo $cabDoc['Ruc'] ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel titleDocumento"><?php echo $cabDoc['NombreDocumento'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">Nº</span>
                    <span class="titleNum_Ruc"><?php echo $cabDoc['NumDocumento'] ?></span>
                </td>
              
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">NÚMERO DE AUTORIZACIÓN</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span ><?php echo $cabDoc['AutorizacionSRI'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">FECHA Y HORA AUTORIZACIÓN</span>
                    <span><?php echo $cabDoc['FechaAutorizacion'];  ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">AMBIENTE</span>
                    <span><?php echo ($cabDoc['Ambiente']=='1')?'PRUEBA':'PRODUCCIÓN'; ?></span>
                </td>
               
            </tr>
            <tr>
                <td>
                    <span class="titleLabel">EMISIÓN</span>
                    <span><?php echo ($cabDoc['TipoEmision']=='1')?'NORMAL':'INDISPONIBILIDAD DEL SISTEMA'; ?></span>
                </td>
                
            </tr>
            <tr>
                <td >
                    <span class="titleLabel">CLAVE DE ACCESO</span>
                </td>
            </tr>
            <tr>
                <td>
                
                    <?php //echo CHtml::image(Yii::app()->params['seaBarra'] .$cabDoc['IdentificacionComprador']. '.png', 'Utimpor', array('width' => '280px', 'height' => '20px')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span><?php echo $cabDoc['ClaveAcceso'] ?></span>
                </td>
            </tr>
        </tbody>
        
    </table>
</div>