<?php

namespace Tests\Feature;

use App\Filament\Resources\Sales\Deliveries\Pages\CreateDelivery;
use App\Models\Company\TaxCode;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesOrder;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

/** A line's link to an upstream line, sent by the browser, is checked: the same customer, and the upstream price. */
class SourceLineGuardTest extends TestCase
{
    public function test_a_line_cannot_point_at_another_customers_order(): void
    {
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $this->seed();
        $this->actingAsAdmin();
        $mine = $this->sampleCustomer();
        $theirs = $this->sampleCustomer(['number' => 'C-00002', 'name' => 'Other Co']);
        $item = $this->sampleItem();
        $order = SalesOrder::query()->create(['number' => 'SO-THEIRS', 'trans_date' => '2026-11-10', 'customer_id' => $theirs->id, 'taxable' => false, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $orderLine = $order->lines()->create(['sort' => 0, 'item_id' => $item->id, 'quantity' => 5, 'unit_id' => $item->unit1_id, 'base_quantity' => 5, 'unit_price' => 150_000, 'tax_code_id' => TaxCode::default()->id, 'warehouse_id' => Warehouse::default()->id]);

        Livewire::test(CreateDelivery::class)
            ->fillForm(['customer_id' => $mine->id, 'trans_date' => '2026-11-16'])
            ->set('data.lines', ['a' => ['item_id' => $item->id, 'quantity' => 5, 'unit_id' => $item->unit1_id, 'unit_price' => 150_000, 'discount_percent' => 0, 'discount_amount' => 0,
                'tax_code_id' => TaxCode::default()->id, 'warehouse_id' => Warehouse::default()->id, 'source_line_type' => 'sales_order_line', 'source_line_id' => $orderLine->id]])
            ->call('create')
            ->assertHasErrors('data.lines');

        $this->assertSame(0, Delivery::query()->count());
        $this->assertSame('0.0000', $orderLine->fresh()->processed_quantity, 'the other customer\'s order is untouched');
    }
}
