<?php
    return CMap::mergeArray(
        // наследуемся от main.php
        require(dirname(__FILE__).'/main.php'),

        array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=twt_test_migr;',
                    'username'=>'root',
                    'password'=>'123456',
                    'charset' => 'utf8',

                    'schemaCachingDuration' => 10,
                    'enableParamLogging' => true,
                    'enableProfiling' => true,
                ),
            ),
        )
    );