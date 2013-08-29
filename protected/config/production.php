<?php
    return CMap::mergeArray(
        // наследуемся от main.php
        require(dirname(__FILE__).'/main.php'),

        array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=10.10.10.26;dbname=twt_soap_test;',
                    'username'=>'artektiv',
                    'password'=>'7SBNYFUS4w',

                    'charset' => 'utf8',
                    'schemaCachingDuration' => 3600,
                    'enableParamLogging' => false,
                    'enableProfiling' => false,
                ),
            ),
        )
    );