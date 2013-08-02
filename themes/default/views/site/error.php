<?php $this->layout = '//layouts/clean'; ?>
<br><br>
<div class="error404">
    <img src="<?php echo $this->assetUrl; ?>/images/mac.png" alt="" />
    <div class="e404">
        <h1><?php echo $code;?></h1>
        
        <div class="title-error">Ooopss... your crashed</div>
        
        <p><?php echo CHtml::encode($message); ?></p>
        <a href="<?php echo absUrl('/'); ?>" class="button orange">Get me back to homepage!</a>
    </div>
</div>