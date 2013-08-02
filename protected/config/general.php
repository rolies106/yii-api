<?php
/**
 * Main Configuration file
 */

$slugPattern = '[a-z0-9-_.]+';

Yii::setPathOfAlias('widgetUser', 'protected/modules/user/components/widgets');
Yii::setPathOfAlias('widget', 'protected/components/widgets');

return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'id'=>'restapi',
    'name'=>'Rest API',

    'preload'=>array(
        'log',
    ),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.modules.user.models.*',
        'application.modules.user.components.*',
    ),

    'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'beautiplan',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
        ),   

        # User module
        'user'=>array(
            # encrypting method (php hash function)
            'hash' => 'md5',

            # send activation email
            'sendActivationMail' => false,

            # allow access for non-activated users
            'loginNotActiv' => false,

            # activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => true,

            # automatically login from registration
            'autoLogin' => true,

            # registration path
            'registrationUrl' => array('/user/registration'),

            # recovery password path
            'recoveryUrl' => array('/user/recovery'),

            # login form path
            'loginUrl' => array('/'),

            # page after login
            'returnUrl' => array('/'),

            # page after logout
            'returnLogoutUrl' => array('/'),

            'captcha' => array('registration'=>false)
        ),        
    ),

    // application components
    'components'=>array(

        'user' => array(
            'allowAutoLogin' => true
        ),

        # Clientscript configurations
        'clientScript'=>array(
            # Using Less
            'class' => 'application.components.LClientScript.LClientScript',
            'caching' => (defined('YII_DEBUG')) ? !YII_DEBUG : true,
            'compress' => (defined('YII_DEBUG')) ? !YII_DEBUG : true,
            'importDir' => array(),

            # this is to prevent jquery-ui.css is loaded
            'scriptMap'=>array(
                'jquery-ui.css'=>false,
            ),
        ),

        # Simple curl component
        'curl' => array(
            'class' => 'application.components.sixreps.CurlRequests',
        ),

        # Url Management
        'urlManager'=>array(
            'urlFormat'=>'path',
            'rules'=>array(
                # API Auth
                '<controller:(auth)>'=>'api/<controller>',
                '<controller:(auth)>/<action:\w+>'=>'api/<controller>/<action>',
                
                # General Page
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
            ),
            'showScriptName' => false,
        ),

        'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
        ),
    ),

    # Application Params
    'params'=>array(),
);