<?php
    return CMap::mergeArray(
        // наследуемся от main.php
        require(dirname(__FILE__) . '/main.php'),

        array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=twt_soap_test;',
                    'username'=>'root',
                    'password'=>'yfh11rjv56fy',
                    'charset' => 'utf8',

                    'schemaCachingDuration' => 10,
                    'enableParamLogging' => true,
                    'enableProfiling' => true,
                ),
            ),
        )
    );