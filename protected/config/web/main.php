<?php
Yii::setPathOfAlias('bootstrap', dirname(__FILE__) . '/../../extensions/bootstrap');

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..',
    'name' => Yii::t('app', 'TWT SOAP Тесты. Версия 2'),
    'sourceLanguage' => 'root',
    'language' => 'ru',
    'defaultController' => 'service',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
    ),

    /*'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '1111',
            'ipFilters' => array('127.0.0.1', '::1', '192.168.1.*'),
            'generatorPaths' => array(
                'bootstrap.gii',
            ),
        ),
    ),*/

    // application components
    'components' => array(
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap',
        ),

        'cache' => array(
            'class' => 'CFileCache'
        ),

        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),

        'authManager' => array(
            // показываем ошибки только в режиме отладки
            'showErrors' => YII_DEBUG
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    // направляем результаты профайлинга в ProfileLogRoute (отображается
                    // внизу страницы)
                    'class' => 'CProfileLogRoute',
                    'levels' => 'profile',
                    'enabled' => true,
                ),
                array(
                    'class' => 'CWebLogRoute',
                    'categories' => 'application',
                    'levels' => 'error, warning, trace, profile, info',
                ),
                array(
                    'class' => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                    'ipFilters' => array('127.0.0.1', '192.168.1.*'),
                ),
                array(
                    'class' => 'CWebLogRoute',
                    'categories' => 'application',
                    'showInFireBug' => true
                ),
                array(
                    'class' => 'CEmailLogRoute',
                    'categories' => 'error',
                    'emails' => array('skybardpf@artektiv.ru'),
                    'sentFrom' => 'error@twt-soap-test.artektiv.ru',
                    'subject' => 'Error at twt-soap-test.artektiv.ru'
                ),
            ),
        ),

        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'caseSensitive' => false,
            'rules' => array(),
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'skybardpf@artektiv.ru',
    ),
);