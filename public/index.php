<?php 
$yii = 'yii.php';

//для php < 5.4
defined('JSON_UNESCAPED_UNICODE') or define('JSON_UNESCAPED_UNICODE', 0);

// change the following paths if necessary
if ($_SERVER['HTTP_HOST'] == 'twt-soap-test') {
    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

    $config = dirname(__FILE__).'/../protected/config/web/local.php';

} elseif ($_SERVER['HTTP_HOST'] == 'twt-soap-test.skybardpf.devel') {
    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

    $config = dirname(__FILE__).'/../protected/config/web/devel.php';
} else  {
    defined('YII_DEBUG') or define('YII_DEBUG',false);

    $config = dirname(__FILE__).'/../protected/config/web/production.php';
}

mb_internal_encoding('UTF-8');

require_once($yii);
Yii::createWebApplication($config)->run();