<?php

namespace Tests\Feature\Domain;

use App\Domain\Access\Hak;
use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Access\MenuKey;
use App\Models\Company\TaxCode;
use App\Models\Settings\AccessGroup;
use App\Models\User;
use Database\Seeders\AccessGroupSeeder;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class HakAksesTest extends TestCase
{
    public function test_an_operator_without_groups_has_no_rights_and_an_administrator_has_all(): void
    {
        $operator = User::factory()->create();
        $admin = User::factory()->create(['access_type' => 'administrator']);
        $akses = app(HakAkses::class);

        $this->assertFalse($akses->allows($operator, MenuKey::TaxCodes, Hak::View));
        $this->assertTrue($akses->allows($admin, MenuKey::TaxCodes, Hak::Delete));
        $this->assertTrue($akses->allowsSpecial($admin, HakKhusus::OpenClosedPeriod));
        $this->assertFalse($akses->allows(null, MenuKey::TaxCodes, Hak::View));
    }

    public function test_group_rights_grant_and_overrides_win(): void
    {
        $user = User::factory()->create();
        $group = AccessGroup::query()->create(['name' => 'Clerks']);
        $group->syncRights([MenuKey::TaxCodes->value => ['view', 'create'], MenuKey::Branches->value => ['view']]);
        $group->syncSpecialRights(['see_cost']);
        $group->users()->attach($user);

        $akses = app(HakAkses::class);
        $this->assertTrue($akses->allows($user, MenuKey::TaxCodes, Hak::View));
        $this->assertTrue($akses->allows($user, MenuKey::TaxCodes, Hak::Create));
        $this->assertFalse($akses->allows($user, MenuKey::TaxCodes, Hak::Delete));
        $this->assertFalse($akses->allows($user, MenuKey::Currencies, Hak::View));
        $this->assertTrue($akses->allowsSpecial($user, HakKhusus::SeeCost));
        $this->assertFalse($akses->allowsSpecial($user, HakKhusus::OpenClosedPeriod));

        $user->rightOverrides()->create(['menu_key' => MenuKey::TaxCodes->value, 'right' => 'view', 'allowed' => false]);
        $user->rightOverrides()->create(['menu_key' => MenuKey::Currencies->value, 'right' => 'print', 'allowed' => true]);
        $akses->forget($user);

        $this->assertFalse($akses->allows($user, MenuKey::TaxCodes, Hak::View), 'a deny override beats the group');
        $this->assertTrue($akses->allows($user, MenuKey::Currencies, Hak::Print), 'an allow override adds to the group');
    }

    public function test_an_inactive_account_loses_everything(): void
    {
        $admin = User::factory()->create(['access_type' => 'administrator', 'is_active' => false]);

        $this->assertFalse(app(HakAkses::class)->allows($admin, MenuKey::TaxCodes, Hak::View));
    }

    public function test_the_gate_answers_model_abilities_from_the_matrix(): void
    {
        $user = User::factory()->create();
        $group = AccessGroup::query()->create(['name' => 'Tax clerks']);
        $group->syncRights([MenuKey::TaxCodes->value => ['view', 'update']]);
        $group->users()->attach($user);
        $this->actingAs($user);

        $code = TaxCode::query()->create(['tax_type' => 'vat', 'description' => 'VAT', 'rate_percent' => 12]);

        $this->assertTrue(Gate::allows('update', $code));
        $this->assertTrue(Gate::allows('viewAny', TaxCode::class));
        $this->assertFalse(Gate::allows('delete', $code));
        $this->assertFalse(Gate::allows('create', TaxCode::class));
    }

    public function test_copying_rights_reproduces_a_group(): void
    {
        $source = AccessGroup::query()->create(['name' => 'Source']);
        $source->syncRights([MenuKey::TaxCodes->value => ['view', 'print']]);
        $source->syncSpecialRights(['see_cost', 'export_data']);
        $target = AccessGroup::query()->create(['name' => 'Target']);

        $target->copyRightsFrom($source);

        $this->assertSame($source->rightsMatrix(), $target->fresh()->load('rights')->rightsMatrix());
        $this->assertEqualsCanonicalizing(['see_cost', 'export_data'], $target->specialRights()->pluck('right')->all());
    }

    public function test_the_seeded_groups_reproduce_the_role_table(): void
    {
        $this->seed(AccessGroupSeeder::class);
        $akses = app(HakAkses::class);
        $member = function (string $group): User {
            $user = User::factory()->create();
            AccessGroup::query()->where('name', $group)->firstOrFail()->users()->attach($user);

            return $user;
        };

        $sales = $member('Sales');
        $this->assertTrue($akses->allows($sales, MenuKey::SalesOrders, Hak::Create));
        $this->assertFalse($akses->allows($sales, MenuKey::SalesReceipts, Hak::Create), 'sales cannot confirm payment');
        $this->assertFalse($akses->allowsSpecial($sales, HakKhusus::SeeCost), 'sales cannot see cost');
        $this->assertFalse($akses->allowsSpecial($sales, HakKhusus::ApproveTransactions), 'sales cannot approve credit');

        $finance = $member('Finance');
        $this->assertTrue($akses->allows($finance, MenuKey::SalesReceipts, Hak::Create));
        $this->assertFalse($akses->allows($finance, MenuKey::PriceAndDiscountAdjustments, Hak::Create), 'finance does not set prices');

        $inventory = $member('Inventory');
        $this->assertTrue($akses->allows($inventory, MenuKey::ItemsAndServices, Hak::Update));
        $this->assertFalse($akses->allowsSpecial($inventory, HakKhusus::SeeCreditData));

        $warehouse = $member('Warehouse');
        $this->assertTrue($akses->allows($warehouse, MenuKey::DeliveryOrders, Hak::Update));
        $this->assertFalse($akses->allows($warehouse, MenuKey::ItemsAndServices, Hak::Update), 'warehouse does not edit the catalogue');

        $marketing = $member('Marketing');
        $this->assertTrue($akses->allowsSpecial($marketing, HakKhusus::ApproveTransactions));
        $this->assertFalse($akses->allows($marketing, MenuKey::SalesReceipts, Hak::Create));
    }
}
