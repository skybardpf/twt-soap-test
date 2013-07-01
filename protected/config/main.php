<?php

//var_dump(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'));die;

Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');
mb_internal_encoding('UTF-8');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SOAP Unit Tests',
	'sourceLanguage' => 'root',
	'language' => 'ru',
	'defaultController' => 'servers',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.commands.*',
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1111',
			'ipFilters'=>array('127.0.0.1','::1','192.168.1.*'),
			'generatorPaths'=>array(
				'bootstrap.gii',
			),
		),
	),

	// application components
	'components'=>array(
//        'tests_actions'=>array(
//            'class'=>'ext.tests_actions.CreateAction'
//        ),

		'bootstrap'=>array(
			'class'=>'bootstrap.components.Bootstrap',
		),

		'cache' => array(
			'class' => 'CFileCache'
		),

		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/soap-test.db',
			'schemaCachingDuration' => YII_DEBUG ? 10 : 3600,
			'enableParamLogging' => YII_DEBUG,
			'enableProfiling' => YII_DEBUG
		),

		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				array(
					'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
					'ipFilters'=>array('127.0.0.1','192.168.1.*'),
				),

			),
		),

        'clientScript' => array(
            'scriptMap' => array(
                'jquery.js'     => 'http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.js',
                'jquery.min.js' => 'http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js',
            )
        ),

		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'caseSensitive' => false,
			'rules'=>array(
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);