<?php

namespace Tests\Feature;

use App\Filament\Modul;
use App\Models\User;
use Filament\Facades\Filament;
use Tests\TestCase;

class PanelBootTest extends TestCase
{
    public function test_the_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee("August's ERP");
    }

    public function test_an_active_administrator_reaches_the_dashboard(): void
    {
        $this->actingAsAdmin();

        $this->get('/admin')->assertOk()->assertSee('Sales this month');
    }

    public function test_an_inactive_account_cannot_open_the_panel(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => false]));

        $this->get('/admin')->assertForbidden();
    }

    public function test_the_sidebar_holds_the_ten_modules_in_the_studied_order(): void
    {
        $this->actingAsAdmin();

        $groups = collect(Filament::getPanel('admin')->getNavigationGroups())
            ->map(fn ($group) => $group->getLabel())
            ->values()
            ->all();

        $this->assertSame(
            array_map(fn (Modul $m) => $m->getLabel(), Modul::cases()),
            $groups,
        );
        $this->assertSame(['Settings', 'Company', 'General Ledger', 'Cash & Bank', 'Sales', 'Purchasing', 'Inventory', 'Fixed Assets', 'Tax', 'Reports'], $groups);
    }

    public function test_the_panel_speaks_english(): void
    {
        $this->assertSame('en', app()->getLocale());
        $this->assertSame('Sales Invoices', __('menu.screens.customer__sales-invoice'));
    }
}
