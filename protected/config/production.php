<?php
    return CMap::mergeArray(
        // наследуемся от main.php
        require(dirname(__FILE__).'/main.php'),

        array(
            'components' => array(
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=twt_soap_test;', // TODO изменить
                    'username'=>'root', // TODO изменить
                    'password'=>'123', // TODO изменить

                    'schemaCachingDuration' => 3600,
                    'enableParamLogging' => false,
                    'enableProfiling' => false,
                ),
            ),
        )
    );