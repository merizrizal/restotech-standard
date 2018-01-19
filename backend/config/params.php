<?php
return [
    'maskMoneyOptions' => [
        'prefix' => 'Rp ',
        'suffix' => '',
        'affixesStay' => true,
        'thousands' => '.',
        'decimal' => ',',
        'precision' => 0, 
        'allowZero' => false,
        'allowNegative' => false,
    ],
    'datepickerOptions' => [
        'format' => 'yyyy-mm-dd',
        'autoclose' => true,
        'todayHighlight' => true,
    ],
    'timepickerOptions' => [
        'showMeridian' => false,
        'defaultTime' => false,
    ],
    'errMysql' => [
        '1451' => '<br>Data ini terkait dengan data yang terdapat pada modul yang lain.',
    ],
];
