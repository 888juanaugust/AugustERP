<?php

/*
|--------------------------------------------------------------------------
| The client's own layer
|--------------------------------------------------------------------------
|
| Everything that makes one installation different from the template lives
| here and under app/Client; the template never touches either, so merging
| a template update into a client's repository keeps them (see .gitattributes).
|
*/

return [

    // Extra modules of this client, each a class implementing App\Modules\Module.
    // They register after the standard modules of config/modules.php.
    'modules' => [
        // App\Client\Modules\WorkshopModule::class,
    ],

    // Optional modules this client starts with, by module key, when erp:install
    // is not told --enable or --disable: ['payroll' => true, 'fixed-assets' => false].
    'features' => [],

    // The panel's colours; any key left out keeps the template's.
    'theme' => [
        'colors' => [
            // 'primary' => '#0f766e',
        ],
    ],

];
