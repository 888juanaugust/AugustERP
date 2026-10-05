<?php

use App\Client\Modules\DeliveryRoutesModule;
use App\Client\Screens\ClientScreen;

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
        DeliveryRoutesModule::class,
    ],

    // The client's own screen keys: string-backed enums implementing
    // App\Domain\Access\ScreenKey, values starting "client__". Rights, menus and
    // the standard's pages pick them up with the template's MenuKey.
    'screens' => [
        ClientScreen::class,
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
