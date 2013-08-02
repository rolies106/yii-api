<?php echo CHtml::beginForm(null, 'post', array('name'=>'login')); ?>
    <div class="form-account">
        <div class="form-heading">
            <h3><?php echo Yii::t(ROOT_THEME, 'Log In'); ?></h3>
        </div>
        
        <div class="form-entry">
            <?php echo CHtml::errorSummary($model, null, null, array('class' => 'errorSummary alert alert-error')); ?>

            <p>
                <?php echo CHtml::activeLabelEx($model,'username'); ?>
                <?php echo CHtml::activeTextField($model,'username') ?>
            </p>
            <p>
                <?php echo CHtml::activeLabelEx($model,'password'); ?>
                <?php echo CHtml::activePasswordField($model,'password') ?>
            </p>
            <p>
                <label class="rememberme">
                    <?php echo CHtml::activeCheckBox($model,'rememberMe'); ?>
                    Remember Me
                </label>
            </p>
            <button type="submit" name="submit" class="button orange"><?php echo Yii::t(ROOT_THEME, 'Login'); ?></button>
        </div>
    </div>
<?php echo CHtml::endForm(); ?>