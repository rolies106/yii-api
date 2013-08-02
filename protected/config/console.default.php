<?php
date_default_timezone_set('UTC');

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../shortcode.php');

return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',

    'aliases' => array(
        'webroot' => realpath(__DIR__.'/../..'),
    ),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
    ),

    'modules' => array(
   
    ),

    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=rolies_api',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'api_',
        ),
    )
);