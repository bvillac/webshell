<div>
    <table style="width:100mm" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'DNI') ?></span>
                    <span class="titleNum_Ruc"><?php echo Yii::app()->getSession()->get('Ruc', FALSE) ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel titleDocumento"><?php echo $cabFact['NombreDocumento'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Number') ?></span>
                    <span class="titleNum_Ruc"><?php echo $cabFact['NumDocumento'] ?></span>
                </td>
              
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Authorization number') ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span ><?php echo $cabFact['AutorizacionSRI'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'DATE AND TIME AUTHORIZATION') ?></span>
                    <span><?php echo ($cabFact['FechaAutorizacion']<>'')?date(Yii::app()->params["datebytime"],strtotime($cabFact['FechaAutorizacion'])):'';  ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'ENVIRONMENT') ?></span>
                    <span><?php echo ($cabFact['Ambiente']=='1')?Yii::t('DOCUMENTOS', 'TEST'):Yii::t('DOCUMENTOS', 'PRODUCTION'); ?></span>
                </td>
               
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'BROADCASTING') ?></span>
                    <span><?php echo ($cabFact['TipoEmision']=='1')?Yii::t('DOCUMENTOS', 'NORMAL'):Yii::t('DOCUMENTOS', 'SYSTEM UNAVAILABLE'); ?></span>
                </td>
                
            </tr>
            <tr>
                <td >
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'PASSWORD') ?></span>
                </td>
            </tr>
            <tr>
                <td>
                
                    <?php echo CHtml::image(Yii::app()->params['seaBarra'] .$cabFact['IdentificacionComprador']. '.png', 'Utimpor', array('width' => '280px', 'height' => '20px')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span><?php echo $cabFact['ClaveAcceso'] ?></span>
                </td>
            </tr>
        </tbody>
        
    </table>
</div>