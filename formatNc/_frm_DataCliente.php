<div>
    <table style="width:200mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Social reason and last name') ?></span>
                    <span><?php echo $cabFact['RazonSocialComprador'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Identification') ?></span>
                    <span><?php echo $cabFact['IdentificacionComprador'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Date of issue') ?></span>
                    <span><?php echo date(Yii::app()->params["datebydefault"],strtotime($cabFact['FechaEmision'])) ?></span>
                </td>
                <td>
                    
                </td>
            </tr>
        </tbody>
    </table>
</div>