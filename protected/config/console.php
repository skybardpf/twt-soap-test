<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
mb_internal_encoding('UTF-8');
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SOAP Unit Tests',
	'sourceLanguage' => 'root',
	'language' => 'ru',

	// preloading 'log' component
	'preload'=>array('log'),

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.commands.*',
	),

	// application components
	'components'=>array(
		'cache' => array(
			'class' => 'CFileCache'
		),

		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/soap-test.db',
			'schemaCachingDuration' => YII_DEBUG ? 10 : 3600,
			'enableParamLogging' => YII_DEBUG,
			'enableProfiling' => YII_DEBUG
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
);