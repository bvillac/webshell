<div>
    <table style="width:200mm;" >
        <tbody>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Proof of purchase') ?></span>
                    <span><?php echo $destDoc[0]['NumDocSustento'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Date of issue') ?></span>
                    <span><?php echo ($destDoc[0]['FechaEmisionDocSustento']<>'0000-00-00')?date(Yii::app()->params["datebydefault"],strtotime($destDoc[0]['FechaEmisionDocSustento'])):'';  ?></span>
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Authorization Number') ?></span>
                    <span><?php echo $destDoc[0]['NumAutDocSustento'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Customs document') ?></span>
                    <span><?php echo $destDoc[0]['DocAduaneroUnico'] ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Plea transfer') ?></span>
                    <span><?php echo $destDoc[0]['MotivoTraslado'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Establishment destination code') ?></span>
                    <span><?php echo $destDoc[0]['CodEstabDestino'] ?></span>
                </td>
                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Arrival destination point') ?></span>
                    <span><?php echo $destDoc[0]['DirDestinatario'] ?></span>
                </td>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Route') ?></span>
                    <span><?php echo $destDoc[0]['Ruta'] ?></span>
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Arrival Identification') ?></span>
                    <span><?php echo $destDoc[0]['IdentificacionDestinatario'] ?></span>
                </td>
                <td>
                    
                </td>                
            </tr>
            <tr>
                <td>
                    <span class="titleLabel"><?php echo Yii::t('DOCUMENTOS', 'Social reason and last name') ?></span>
                    <span><?php echo $destDoc[0]['RazonSocialDestinatario'] ?></span>
                </td>
                <td>
                    
                </td>
            </tr>
            
        </tbody>
    </table>
</div>