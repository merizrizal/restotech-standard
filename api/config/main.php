<?php
$params = array_merge(
    require(dirname(dirname(__DIR__)) . '/common/config/params.php'),
    require(__DIR__ . '/params.php')
);

$config = [
    'id' => 'app-api',
    'language' => 'id',
    'name' => 'PoGAPfWbaiS2knCv69F+J5NP3CDsmEPL/fa0bH8PyK8=',
    'bootstrap' => ['log'],
    'defaultRoute' => $mainModule . '/home/load-menu',
    'modules' => [
        
    ],
    'components' => [
        'user' => [
            'identityClass' => 'restotech\standard\backend\models\User',
            'enableSession' => false,
            'loginUrl' => null,
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
            'errorAction' => $mainModule . '/site/error',
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
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

return yii\helpers\ArrayHelper::merge(
        $config,
        require(dirname(dirname(__DIR__)) . '/common/config/main.php')
);