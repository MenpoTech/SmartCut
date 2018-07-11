<?php
ini_set('max_execution_time', 300);
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'SMART',
	'name' => 'SMARTCUT',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'timezone' => 'Asia/Kolkata',
    'aliases' => [
        '@remark'=>'@vendor/remark_theme',
    ],
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'request' => [
            'cookieValidationKey' => 'xyctuyvibonp',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\MstUsers',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => DatabaseConfig::getLiveDbConnection(),
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'showScriptName' => false,
			'enablePrettyUrl' => true,
			'rules' => array(
			   	'<controller:\w+>/<id:\d+>' => '<controller>/view',
          			'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
          			'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
        ],
        'view' => [
			 'theme' => [
				 'pathMap' => [
//					'@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
                     '@app/views' => '@vendor/remark_theme'
				 ],
			 ],
		],
        'common' => [
            'class'=>'app\components\CommonComponent',
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en_US',
                ],
            ]
        ]
	],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
//    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gridview'] = [ 'class' => '\kartik\grid\Module' ];
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
