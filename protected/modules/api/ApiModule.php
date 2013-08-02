<?php
class ApiModule extends CWebModule
{
    private $_version = "1.0beta";
    
    static private $_uid;
    static private $_oauth;
    private $_debug = false;

    public function init()
    {
        Yii::app()->homeUrl = array('/api');
        $api_url = Yii::app()->createAbsoluteUrl('/api');

        $this->setImport(array(
            'api.models.*',
            'api.components.*',
            )
        );
    }

    public function oauth2_init()
    {
        Yii::import('application.modules.api.extensions.oauth2.YiiOAuth2');
        YiiOAuth2::instance();
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            $array = array("default");
            if(!in_array($controller->id,$array))
            {
                $this->oauth2_init();
                if($controller->id != 'auth'){
                    $this->authorization();
                }
            }
            return true;
        }
        else
            return false;
    }

    public function authorization()
    {
        $token = YiiOAuth2::instance()->verifyToken();
        
        // If we have an user_id, then login as that user (for this request)
        if($token && isset($token['user_id']))
        {
            self::setUid($token['user_id']);
            self::$_oauth = true;
        }
        else
        {
            $msg = "Can't verify request, missing oauth_consumer_key or oauth_token";
            throw new CHttpException(401,$msg);
            exit();
        }
    }

    public static function setUid($uid)
    {
        if(empty($uid))
        {
            $msg =  "authorization failed, missing login user id.";
            throw new CHttpException(401,$msg);
            exit();
        }

        self::$_uid = $uid;
    }

    public static function getUid()
    {
        if(empty(self::$_uid))
        {
            $msg =  "Not found";
            throw new CHttpException(403,$msg);
            exit();
        }

        return self::$_uid;
    }

    public function token()
    {
        return Oauth2Tokens::model();
    }

    public function client()
    {
        return Oauth2Clients::model();
    }
}