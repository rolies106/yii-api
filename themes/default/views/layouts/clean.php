<!DOCTYPE html>
<!--[if lte IE 8]>              <html class="ie8 no-js" lang="en">     <![endif]-->
<!--[if IE 9]>                  <html class="ie9 no-js" lang="en">     <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="not-ie no-js" lang="en">  <!--<![endif]-->
<head>
    <?php
        Yii::app()->clientScript
            ->registerCssFile('http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz|Open+Sans:400,600,700|Oswald|Electrolize')
            ->registerCssFile($this->assetUrl . '/css/style.css')
            // ->registerCssFile($this->assetUrl . '/css/skeleton.css')
            // ->registerCssFile($this->assetUrl . '/css/bootstrap.min.css')
            // ->registerCssFile($this->assetUrl . '/sliders/flexslider/flexslider.css')
            // ->registerCssFile($this->assetUrl . '/fancybox/jquery.fancybox.css')
            // ->registerCssFile($this->vendorUrl . '/gritter/css/jquery.gritter.css')
            // ->registerScriptFile($this->assetUrl . '/sliders/flexslider/jquery.flexslider-min.js', CClientScript::POS_END)
            // ->registerScriptFile('http://maps.google.com/maps/api/js?sensor=false', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/jquery.gmap.min.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/jquery-impromptu.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/custom.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/jquery.easing.1.3.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/jquery.cycle.all.min.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/respond.min.js', CClientScript::POS_END)
            // ->registerScriptFile($this->vendorUrl . '/fancybox/jquery.fancybox.js', CClientScript::POS_END)
            // ->registerScriptFile($this->vendorUrl . '/gritter/js/jquery.gritter.min.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/js/bootstrap.min.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/changer/js/changer.js', CClientScript::POS_END)
            // ->registerScriptFile($this->assetUrl . '/changer/js/colorpicker.js', CClientScript::POS_END)
            ;
    ?>
    <!--[if lt IE 9]>
        <?php
                Yii::app()->clientScript
                    ->registerScriptFile($this->assetUrl . '/js/selectivizr-and-extra-selectors.min.js', CClientScript::POS_END);
        ?>
    <![endif]-->
    <?php $this->widget('widget.GeneralFlashMessage.GeneralFlashMessage', array()); ?>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="<?php echo Yii::app()->params['description']; ?>" />
    <meta name="author" content="rolies106" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
    <title><?php echo $this->pageTitle; ?></title>
    <link rel="shortcut" href="<?php echo $this->assetUrl; ?>/images/favicon.ico" />
<body class="menu-1 h-style-1 text-1">

<div class="wrap">    
    <div class="main">
        <?php echo $content; ?>
    </div>
</div>
</body>
</html>
