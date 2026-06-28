<?php

return [
    'package' => [
        'name' => 'restotech/standard',
        'namespace' => 'Restotech\\Standard',
        'version' => 'dev',
    ],
    'route_prefixes' => [
        'back_office' => 'restotech/admin',
        'pos' => 'restotech/pos',
        'api' => 'api/restotech/v1',
    ],
    'routes' => [
        'back_office' => [
            'enabled' => true,
        ],
        'pos' => [
            'enabled' => true,
        ],
        'api' => [
            'enabled' => true,
        ],
    ],
    'pos' => [
        'tax_rate' => 0,
        'service_charge_rate' => 0,
    ],
    'views' => [
        'theme' => 'minimal',
    ],
];
