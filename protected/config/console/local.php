<?php

mb_internal_encoding('UTF-8');
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..',
    'name' => 'SOAP Unit Tests',
    'sourceLanguage' => 'root',
    'language' => 'ru',

    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=twt_soap_test;',
            'username' => 'root',
            'password' => 'yfh11rjv56fy',
            'charset' => 'utf8',

            'schemaCachingDuration' => 10,
            'enableParamLogging' => true,
            'enableProfiling' => true,
        ),
    ),
);