<div>
    <table style="width:200mm;" class="marcoDiv">
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Proof amending') ?></span>
                </td>
                <td>
                    <span><?php echo Yii::t('DOCUMENTOS', 'Invoice').' '.$cabFact['NumDocModificado'] ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Issued (Proof to change)') ?></span>
                </td>
                <td>
                    <span><?php echo date(Yii::app()->params["datebydefault"],strtotime($cabFact['FechaEmisionDocModificado'])) ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Reason for Change') ?></span>
                </td>
                <td>
                    <span><?php echo $cabFact['MotivoModificacion'] ?></span>
                </td>
            </tr>
            
        </tbody>
    </table>
</div>