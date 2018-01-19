<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            //'locale' => 'id-ID',
            'timeZone' => 'UTC',
            'dateFormat' => 'yyyy-MM-dd',
            'timeFormat' => 'HH:mm',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm',                        
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'Rp ',
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 0,
                NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ]
       ],
    ],
    
];
