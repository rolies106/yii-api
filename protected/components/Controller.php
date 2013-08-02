<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout='//layouts/main-column2';

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $assetUrl;

    public $breadcrumbs;

    public $vendorUrl;

    public function init() {

        if ($this->layout != 'ajax' && $this->layout != 'json') {
            $parentThemeFolder = ltrim(Yii::app()->theme->baseUrl, '/');
            defined('ROOT_THEME') or define('ROOT_THEME', $parentThemeFolder);

            $themeURL = ltrim(Yii::app()->theme->baseUrl, '/');
            $this->vendorUrl = app()->getAssetManager()->publish($parentThemeFolder . '/vendors/');
            $this->assetUrl = app()->getAssetManager()->publish($themeURL . '/assets/');

            Yii::app()->clientScript
                ->registerScriptFile($this->assetUrl . '/js/jquery-1.7.2.min.js', CClientScript::POS_HEAD)
                ->registerScriptFile($this->vendorUrl . '/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js', CClientScript::POS_HEAD)
                ->registerScriptFile($this->vendorUrl . '/gritter/js/jquery.gritter.min.js', CClientScript::POS_HEAD)
                ->registerScriptFile($this->vendorUrl . '/modernizr-2.6.2-respond-1.1.0.min.js', CClientScript::POS_HEAD)
                ->registerCssFile($this->vendorUrl . '/gritter/css/jquery.gritter.css')
                ->registerScriptFile($this->vendorUrl . '/core-function.js', CClientScript::POS_HEAD);
        } else if ($this->layout == 'json') {
            header('Content-type: application/json');          
        }
    }

    public function apiVerify($user = null)
    {
        Yii::app()->getModule('api')->oauth2_init();
        $oauth = YiiOAuth2::instance();
        $valid = $oauth->verifyToken();

        return $valid;
    }    

    public function apiDenied($a)
    {
        throw new CHttpException(403, "Invalid Token");
    }
}