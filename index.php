<?php
// change the following paths if necessary
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $yii = dirname(__FILE__).'/../yii/yii.php';
} elseif ($_SERVER['HTTP_HOST'] == 'twt-soap-test.artektiv.ru'){
    $yii = 'yii.php';
}

$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);


//для php < 5.4
defined('JSON_UNESCAPED_UNICODE') or define('JSON_UNESCAPED_UNICODE', 0);

require_once($yii);
require_once(dirname(__FILE__).'/protected/commands/TConsoleRunner/TConsoleRunner.php');
Yii::createWebApplication($config)->run();
