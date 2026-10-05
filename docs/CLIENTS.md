# Clients

August ERP is a template. Each company that runs it gets its own repository,
made from this one, where everything that is only theirs lives in one layer:
`app/Client` and `config/client.php`. The template never touches that layer,
so a client merges every template update without conflicts.

## Start a client repository

1. On GitHub, **Use this template** → a new private repository for the client
   (or clone this one and push it to a new remote).
2. In the clone, keep the template as a remote and turn on the merge driver
   `.gitattributes` relies on:

   ```sh
   git remote add template https://github.com/888juanaugust/AugustERP.git
   git config merge.ours.driver true
   ```

3. Install as usual (`php artisan erp:install`, see `README.md`), with the
   client's modules switched on or off by `--enable` / `--disable`, or by
   `features` in `config/client.php`.

## The client layer

| Where | What |
|---|---|
| `config/client.php` | `modules` (the client's own modules), `screens` (their screen keys), `features` (modules to start with), `theme` (panel colours) |
| `app/Client/Modules/` | Modules implementing `App\Modules\Module` (extend `App\Modules\BaseModule`): their screens, morph names, posting hooks, default seeders, commands and schedule |
| `app/Client/Screens/` | A string-backed enum implementing `App\Domain\Access\ScreenKey`, one case per screen, values starting `client__`. Rights, menus, the access-group matrix and the standard's pages read these with the template's `MenuKey` |
| `app/Client/Filament/Resources`, `app/Client/Filament/Pages` | The client's screens, discovered by the panel; a resource extends `ErpResource` / `MasterResource` and returns its `ScreenKey` from `menuKey()` |
| `app/Client/Models/`, `app/Client/Seeders/` | The client's models and seeders (a module's `defaultSeeders()` runs them on install and `db:seed`) |
| `app/Client/database/migrations/` | The client's migrations, run with the template's; date them after the template migration they build on, and never edit one that has shipped |
| `app/Client/lang/<locale>.json` | The client's strings; they override the template's |
| `app/Client/ClientServiceProvider.php` | Boots last: bindings that replace a template service, extra blockers or ledger writers through `App\Modules\ModuleContext` |
| `tests/Feature/Client/` | The client's tests |

A screen key's value is stored in access rights and user overrides: once a
screen is in use its value never changes.

## Override a standard behaviour

Never edit the template's code in a client repository; the next update would
conflict. Instead, in `ClientServiceProvider::register()` bind the template's
class or interface to the client's own implementation (a subclass of a
domain service, say), or add a blocker or ledger writer in a client module's
`boot()`. If the change would suit every client, propose it to the template
instead.

## Merge a template update

```sh
git fetch template
git merge template/main
php artisan migrate
php artisan erp:standard        # the standard's pages, now with the client's screens
php artisan test
```

`app/Client`, `config/client.php`, the generated pages under `docs/standard`
and `.env.example` keep the client's copy (`merge=ours`). Anything else
merging with a conflict means the client edited a template file: move that
change into the client layer.

## The worked example

`examples/client/` holds a small client module, **Delivery Routes**: a screen
key, a module with a default seeder, a model, a master resource, a migration
and tests. Install it into a client repository with

```sh
sh examples/client/install.sh [path/to/client/repository]
```

CI checks the promise above on every commit (`tools/client/merge-check.sh`): a
client repository made from the previous template commit, with the example
installed and committed, merges the current commit without a conflict and the
example's tests pass.
