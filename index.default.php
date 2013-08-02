<?php
date_default_timezone_set('UTC');

# Sixreps Agent Checker
$agentCheck = dirname(__FILE__).'/sixreps_agent.php';
require_once($agentCheck);

# Run check for current user agent
$agent = new SixrepsAgentDetect;
$user_agent = $agent->check();

if ($user_agent == 'wap') {
    $user_agent = 'mobile';
}

if ($user_agent == 'tablet') {
    $user_agent = 'phone';
}

// Tell config to remember this env
defined('BEAUTI_ENV') or define('BEAUTI_ENV', $user_agent);

// change the following paths if necessary
$yii=dirname(__FILE__).'/../testing/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main-' . $user_agent . '.php';
$shortcode=dirname(__FILE__).'/shortcode.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once($shortcode);
Yii::createWebApplication($config)->run();
