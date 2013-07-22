<?php
    return CMap::mergeArray(
        // наследуемся от main.php
        require(dirname(__FILE__).'/main.php'),

        array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=192.168.0.205;dbname=twt_soap_test;',
                    'username'=>'artektiv',
                    'password'=>'qazwsxedc',

                    'schemaCachingDuration' => 10,
                    'enableParamLogging' => true,
                    'enableProfiling' => true,
                ),
            ),
        )
    );