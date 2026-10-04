<?php

namespace Tests\Feature;

use App\Domain\Access\MenuKey;
use App\Filament\Support\ErpPage;
use App\Filament\Support\ErpResource;
use App\Filament\Support\PlaceholderPage;
use App\Models\User;
use Filament\Facades\Filament;
use Tests\TestCase;

/**
 * The honest counter of what is built: every registered screen opens for an
 * administrator and is refused to an operator without rights.
 */
class ScreenRouteTest extends TestCase
{
    /** @return array<string, string> menu key → index url */
    private function screens(): array
    {
        $panel = Filament::getPanel('admin');
        $urls = [];
        foreach ($panel->getResources() as $resource) {
            if (is_subclass_of($resource, ErpResource::class)) {
                $urls[$resource::menuKey()->value] = $resource::getUrl('index');
            }
        }
        foreach ($panel->getPages() as $page) {
            if (is_subclass_of($page, ErpPage::class)) {
                $urls[$page::menuKey()->value] = $page::getUrl();
            }
        }

        return $urls;
    }

    public function test_every_registered_screen_opens_for_an_administrator(): void
    {
        $this->seed();
        $this->actingAsAdmin();

        $screens = $this->screens();
        $this->assertNotEmpty($screens);

        foreach ($screens as $key => $url) {
            $this->get($url)->assertOk();
            $this->assertNotNull(MenuKey::tryFrom($key));
        }
    }

    public function test_every_registered_screen_is_refused_to_an_operator_without_rights(): void
    {
        $this->seed();
        $this->actingAs(User::factory()->create());

        foreach ($this->screens() as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_the_built_screens_are_registered_and_the_placeholders_are_known(): void
    {
        $built = array_keys($this->screens());
        $placeholders = collect(Filament::getPanel('admin')->getPages())
            ->filter(fn ($page) => is_subclass_of($page, PlaceholderPage::class))
            ->map(fn ($page) => $page::menuKey()->value)
            ->values()
            ->all();

        foreach ([
            MenuKey::Preferences, MenuKey::AccessGroups, MenuKey::Users, MenuKey::Numbering,
            MenuKey::Currencies, MenuKey::Branches, MenuKey::TaxCodes, MenuKey::PaymentTerms,
            MenuKey::ShippingMethods, MenuKey::FOBTerms, MenuKey::ActivityLog,
            // phase 2
            MenuKey::Customers, MenuKey::CustomerCategories, MenuKey::PriceCategories, MenuKey::Vendors, MenuKey::VendorCategories,
            MenuKey::Employees, MenuKey::Contacts, MenuKey::ItemsAndServices, MenuKey::Units, MenuKey::ItemCategories, MenuKey::ItemBrands, MenuKey::Warehouses,
            // phase 3
            MenuKey::ChartOfAccounts, MenuKey::JournalVouchers, MenuKey::ExpenseAccruals, MenuKey::AccountHistory, MenuKey::MonthEndProcess, MenuKey::JournalActivityLog,
        ] as $key) {
            $this->assertContains($key->value, $built, $key->label());
            $this->assertNotContains($key->value, $placeholders, $key->label().' is a placeholder');
        }

        $this->assertEqualsCanonicalizing([
            MenuKey::PrintLayouts->value, MenuKey::TransactionApprovers->value,
            MenuKey::Budgets->value, MenuKey::BudgetMonitor->value, MenuKey::BudgetTransfers->value, MenuKey::PayrollEntries->value,
        ], $placeholders);
    }
}
