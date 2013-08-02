<?php
/*
 * This is configurations that different for each environment
 */

YiiBase::setPathOfAlias('rest', 'application/extensions/restapi/library/rest');

return CMap::mergeArray(
    require(dirname(__FILE__).'/general.php'),
    array(
        // preloading 'log' component
        'preload'=>array(),

        'modules'=>array(
            'api'
        ),

        // application components
        'components'=>array(
            # Define cache management (using redis)
            'cache'=>array(
                'class' => 'system.caching.CFileCache', // If you didn't have redis
            ),

            # Asset management configurations
            'assetManager' => array(
                'linkAssets' => (defined('YII_DEBUG')) ? YII_DEBUG : false,
            ),

            'rest' => array(
                'class' => 'application.components.request.Rest',
                'api_host'  =>'http://api-php.local/',
                'app_id'    =>'1234567890',
                'app_secret'=>'1234567890',
                'signed_request_key' => '1234567890'
            ),

            # Database Connection
            'db'=>array(
                'connectionString' => 'mysql:host=127.0.0.1;dbname=rolies_api',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'tablePrefix' => 'api_',
            ),
        ),

        'theme' => 'default'
    )
);