<?php

namespace Tests\Feature;

use App\Filament\Resources\Sales\SalesOrders\Pages\CreateSalesOrder;
use App\Models\Company\TaxCode;
use App\Models\Inventory\Item;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\Customer;
use App\Models\Sales\SalesOrder;
use App\Models\Settings\AccessGroup;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

/** The selling price is checked on the server: without the right, a line sells at the price in force and nothing is taken off. */
class SellingPriceGuardTest extends TestCase
{
    private Customer $customer;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $this->seed();
        $this->actingAsAdmin();
        $this->customer = $this->sampleCustomer();
        $this->item = $this->sampleItem(); // sells at 150,000
        $sales = User::factory()->create();
        AccessGroup::query()->where('name', 'Sales')->firstOrFail()->users()->attach($sales);
        $this->actingAs($sales);
        $this->freshRequest();
    }

    private function order(array $line, array $header = [])
    {
        $page = Livewire::test(CreateSalesOrder::class)
            ->fillForm(['customer_id' => $this->customer->id, 'trans_date' => '2026-11-16', 'taxable' => false, 'inclusive_tax' => false]);
        foreach ($header as $field => $value) {
            $page->set("data.{$field}", $value); // after the customer, whose pick fills the header's defaults
        }

        return $page->set('data.lines', ['a' => $line + ['item_id' => $this->item->id, 'quantity' => 10, 'unit_id' => $this->item->unit1_id, 'unit_price' => 150_000, 'discount_percent' => 0, 'discount_amount' => 0, 'tax_code_id' => TaxCode::default()->id, 'warehouse_id' => Warehouse::default()->id]])
            ->call('create');
    }

    public function test_a_salesperson_sells_at_the_price_in_force(): void
    {
        $this->order(['unit_price' => 1])->assertHasErrors('data.lines');
        $this->order(['discount_percent' => 50])->assertHasErrors('data.lines');
        $this->order(['discount_amount' => 500_000])->assertHasErrors('data.lines');
        $this->order([], ['discount_percent' => 10])->assertHasErrors('data.lines');
        $this->assertSame(0, SalesOrder::query()->count(), 'nothing was saved');

        $this->order([])->assertHasNoErrors();
        $this->assertSame(1_500_000, SalesOrder::query()->sole()->total);
    }

    public function test_with_the_right_a_price_may_change(): void
    {
        $this->actingAsAdmin();
        $this->freshRequest();
        $this->order(['unit_price' => 140_000])->assertHasNoErrors();
        $this->assertSame(1_400_000, SalesOrder::query()->sole()->total);
    }
}
