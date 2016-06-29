<div>
    <table style="width:100mm" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleRazon"><?php echo strtoupper(Yii::app()->getSession()->get('RazonSocial', FALSE)) ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Dir matrix') ?></span>
                    <span><?php echo strtoupper(Yii::app()->getSession()->get('DireccionMatriz', FALSE)) ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Dir branch') ?></span>
                    <span><?php echo strtoupper(Yii::app()->getSession()->get('DireccionSucursal', FALSE)) ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Special contributor') ?></span>
                    <span><?php echo strtoupper(Yii::app()->getSession()->get('ContribuyenteEspecial', FALSE)) ?></span>
                </td>
               
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'ACCOUNTING REQUIRED TO CARRY') ?></span>
                    <span><?php echo strtoupper(Yii::app()->getSession()->get('ObligadoContabilidad', FALSE)) ?></span>
                </td>
                
            </tr>
            
        </tbody>
        
    </table>
</div>