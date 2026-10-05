<?php

namespace Tests\Feature\Client;

use App\Client\Filament\Resources\DeliveryRoutes\DeliveryRouteResource;
use App\Client\Filament\Resources\DeliveryRoutes\Pages\ManageDeliveryRoutes;
use App\Client\Models\DeliveryRoute;
use App\Client\Screens\ClientScreen;
use App\Domain\Access\Screens;
use App\Models\Settings\AccessGroup;
use App\Models\User;
use App\Modules\ModuleRegistry;
use Livewire\Livewire;
use Tests\TestCase;

/** The worked client module: its screen sits beside the template's, seeded, guarded by its own rights. */
class DeliveryRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAsAdmin();
    }

    public function test_the_client_screen_is_a_screen_of_the_installation(): void
    {
        $this->assertSame(ClientScreen::DeliveryRoutes, Screens::find('client__delivery-routes'));
        $this->assertSame('delivery-routes', app(ModuleRegistry::class)->ownerOf(ClientScreen::DeliveryRoutes)::key());
        $this->assertSame(['City centre', 'Industrial estate'], DeliveryRoute::query()->orderBy('name')->pluck('name')->all(), 'seeded by the module');

        $this->get(DeliveryRouteResource::getUrl())->assertOk()->assertSee('City centre');
        Livewire::test(ManageDeliveryRoutes::class)
            ->callAction('create', ['name' => 'Harbour', 'area' => 'North'])
            ->assertHasNoActionErrors();
        $this->assertSame('North', DeliveryRoute::query()->where('name', 'Harbour')->value('area'));
    }

    public function test_its_rights_are_granted_like_any_other_screen(): void
    {
        $dispatch = AccessGroup::query()->create(['name' => 'Dispatch']);
        $user = User::factory()->create();
        $dispatch->users()->attach($user);
        $this->actingAs($user);

        $this->get(DeliveryRouteResource::getUrl())->assertForbidden();
        $dispatch->syncRights([ClientScreen::DeliveryRoutes->value => ['view']]);
        $this->assertTrue((bool) $dispatch->rights()->where('menu_key', 'client__delivery-routes')->value('can_view'), 'stored like a template screen\'s right');
        $this->freshRequest();
        $this->get(DeliveryRouteResource::getUrl())->assertOk();
    }
}
