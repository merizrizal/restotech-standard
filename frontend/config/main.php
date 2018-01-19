<?php
$params = array_merge(
    require(dirname(dirname(__DIR__)) . '/common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-frontend',
    'language' => 'id',
    'name' => 'PoGAPfWbaiS2knCv69F+J5NP3CDsmEPL/fa0bH8PyK8=',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'home/index',    
    'modules' => [ 
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-restotech', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'restotech',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],        
        'request' => [
            
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,  
            'showScriptName' => false,
            'rules' => [                
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',                
            ]
        ],        
    ],
    'params' => $params,
];

