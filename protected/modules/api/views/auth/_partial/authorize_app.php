<?php echo CHtml::beginForm(null, 'post', array('name'=>'login')); ?>
    <div class="form-account">
        <div class="form-heading">
            <h3><?php echo Yii::t(ROOT_THEME, 'Authorize Application'); ?></h3>
        </div>
        
        <div class="form-entry">
            <?php echo CHtml::errorSummary($model, null, null, array('class' => 'errorSummary alert alert-error')); ?>

            <p>
                Do you want to authorize "<?php echo $app['app_title']; ?>" to connect with your <?php echo Yii::app()->name; ?> account?
            </p>
            
            <?php foreach ($auth_params as $k => $v) { ?>
                <input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>" />
            <?php } ?>

            <button type="submit" name="authorize" class="button orange" value="1"><?php echo Yii::t(ROOT_THEME, 'Yes'); ?></button>
            <button type="submit" name="authorize" class="button orange" value="0"><?php echo Yii::t(ROOT_THEME, 'No'); ?></button>
        </div>
    </div>
<?php echo CHtml::endForm(); ?>