<?php

declare(strict_types=1);

namespace App\Client;

use Illuminate\Support\ServiceProvider;

/**
 * The client's own code lives under app/Client and boots from here, last of
 * all providers, so it can override anything the template registered. In
 * the template this provider is empty on purpose. What belongs here:
 *
 *  - extra modules (listed in config/client.php under "modules"), each with
 *    its own resources, pages, models, migrations and seeders;
 *  - bindings that replace a template service (bind the interface or class
 *    to the client's implementation in register());
 *  - extra blockers, ledger writers or fulfilment chains, wired in boot()
 *    through the same services the standard modules use (see
 *    App\Modules\ModuleContext);
 *  - the client's translations (lang/<locale>.json) and print layouts.
 *
 * Never edit the standard modules for one client; override them from here,
 * so the next template update still merges.
 */
class ClientServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {}
}
