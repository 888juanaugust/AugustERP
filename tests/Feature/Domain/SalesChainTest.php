<?php

namespace Tests\Feature\Domain;

use App\Domain\Inventory\StockQuery;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Domain\Posting\AccountBalances;
use App\Domain\Posting\DocumentRepository;
use App\Domain\Sales\CreditCheck;
use App\Domain\Sales\OrderApproval;
use App\Domain\Sales\PriceResolver;
use App\Models\Company\PaymentTerm;
use App\Models\Company\TaxCode;
use App\Models\GeneralLedger\Account;
use App\Models\Inventory\InventoryAdjustment;
use App\Models\Inventory\Item;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\Customer;
use App\Models\Sales\Delivery;
use App\Models\Sales\PriceCategory;
use App\Models\Sales\SalesDownPayment;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesReceipt;
use App\Models\Sales\SalesReturn;
use App\Models\Sales\SellingPriceAdjustment;
use App\Models\Settings\AccessGroup;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SalesChainTest extends TestCase
{
    private Customer $customer;

    private Item $item;

    private Warehouse $warehouse;

    private TaxCode $vat;

    private DocumentRepository $docs;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-15 09:00:00');
        CarbonImmutable::setTestNow('2026-11-15 09:00:00');
        $this->seed();
        $this->actingAsAdmin();
        $this->customer = Customer::query()->create(['number' => 'C-00001', 'name' => 'Bengkel Maju', 'price_category_id' => PriceCategory::query()->where('is_default', true)->value('id'), 'payment_term_id' => PaymentTerm::default()->id]);
        $this->item = Item::query()->create(['number' => 'ITM-00001', 'name' => 'Brake pad', 'unit1_id' => Unit::query()->where('name', 'PCS')->value('id'), 'sell_price' => 150_000, 'purchase_price' => 100_000]);
        $this->warehouse = Warehouse::default();
        $this->vat = TaxCode::default();
        $this->docs = app(DocumentRepository::class);
        app(Preferensi::class)->set(PreferensiKey::MarketingApprovalRequired, false);

        $opening = InventoryAdjustment::query()->create(['number' => 'ADJ-OPEN', 'trans_date' => '2026-04-01', 'created_by' => auth()->id()]);
        $opening->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'adjustment_type' => 'quantity', 'quantity' => 20, 'unit_id' => $this->item->unit1_id, 'base_quantity' => 20, 'unit_cost' => 100_000, 'total_cost' => 0, 'warehouse_id' => $this->warehouse->id]);
        $this->docs->created($opening);
    }

    private function balance(string $no): int
    {
        return AccountBalances::asOf()[Account::query()->where('no', $no)->value('id')] ?? 0;
    }

    private function order(int $qty, int $price, string $date = '2026-11-01'): SalesOrder
    {
        $order = SalesOrder::query()->create(['number' => 'SO-'.uniqid(), 'trans_date' => $date, 'customer_id' => $this->customer->id, 'taxable' => true, 'inclusive_tax' => false, 'payment_term_id' => $this->customer->payment_term_id, 'approval_status' => app(OrderApproval::class)->initialStatus(), 'created_by' => auth()->id()]);
        $order->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'quantity' => $qty, 'unit_id' => $this->item->unit1_id, 'base_quantity' => $qty, 'unit_price' => $price, 'tax_code_id' => $this->vat->id, 'warehouse_id' => $this->warehouse->id]);
        $order->refreshTotal();
        $this->docs->created($order);

        return $order->fresh();
    }

    private function deliver(SalesOrder $order, int $qty, string $date = '2026-11-03'): Delivery
    {
        $line = $order->lines()->first();
        $delivery = Delivery::query()->create(['number' => 'DO-'.uniqid(), 'trans_date' => $date, 'customer_id' => $this->customer->id, 'taxable' => $order->taxable, 'inclusive_tax' => $order->inclusive_tax, 'created_by' => auth()->id()]);
        $delivery->lines()->create(['sort' => 0, 'item_id' => $line->item_id, 'quantity' => $qty, 'unit_id' => $line->unit_id, 'base_quantity' => $qty, 'unit_price' => $line->unit_price, 'tax_code_id' => $line->tax_code_id, 'warehouse_id' => $line->warehouse_id, 'source_line_type' => 'sales_order_line', 'source_line_id' => $line->id]);
        $delivery->refreshTotal();
        $this->docs->created($delivery);

        return $delivery->fresh();
    }

    public function test_order_delivery_invoice_and_receipt_keep_stock_books_and_statuses_in_step(): void
    {
        $order = $this->order(10, 150_000);
        $this->assertSame(1_665_000, $order->total);
        $this->assertSame('approved', $order->approval_status, 'the rule is off, so the order is approved on entry');

        $delivery = $this->deliver($order, 6);
        $this->assertSame('14.0000', StockQuery::onHand($this->item->id));
        $this->assertSame(600_000, $this->balance('1310'), 'goods delivered, not invoiced, at cost');
        $this->assertSame(1_400_000, $this->balance('1300'));
        $this->assertSame('partial', $order->fresh()->status);

        $deliveryLine = $delivery->lines()->first();
        $invoice = SalesInvoice::query()->create(['number' => 'INV-1', 'trans_date' => '2026-11-05', 'customer_id' => $this->customer->id, 'taxable' => true, 'inclusive_tax' => false, 'payment_term_id' => $this->customer->payment_term_id, 'created_by' => auth()->id()]);
        $invoice->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'quantity' => 6, 'unit_id' => $this->item->unit1_id, 'base_quantity' => 6, 'unit_price' => 150_000, 'tax_code_id' => $this->vat->id, 'warehouse_id' => $this->warehouse->id, 'source_line_type' => 'delivery_line', 'source_line_id' => $deliveryLine->id]);
        $invoice->refreshTotal();
        $this->docs->created($invoice);
        $invoice->refresh();

        $this->assertSame(900_000, $invoice->subtotal);
        $this->assertSame(99_000, $invoice->tax_total);
        $this->assertSame(999_000, $invoice->total);
        $this->assertSame('2026-12-05', $invoice->due_date->toDateString());
        $this->assertSame('processed', $delivery->fresh()->status);
        $this->assertSame(999_000, $this->balance('1200'), 'receivable');
        $this->assertSame(900_000, $this->balance('4100'), 'sales');
        $this->assertSame(99_000, $this->balance('2200'), 'VAT out');
        $this->assertSame(600_000, $this->balance('5100'), 'cost of goods sold at the delivery cost');
        $this->assertSame(0, $this->balance('1310'), 'in-transit cleared');

        $receipt = SalesReceipt::query()->create(['number' => 'CB-1', 'trans_date' => '2026-11-12', 'customer_id' => $this->customer->id, 'bank_account_id' => Account::query()->where('no', '1102')->value('id'), 'created_by' => auth()->id()]);
        $receipt->lines()->create(['sort' => 0, 'receivable_type' => 'sales_invoice', 'receivable_id' => $invoice->id, 'amount' => 990_000, 'discount' => 9_000]);
        $receipt->refreshTotal();
        $this->docs->created($receipt);

        $this->assertSame('paid', $invoice->fresh()->payment_status);
        $this->assertSame(0, $this->balance('1200'));
        $this->assertSame(990_000, $this->balance('1102'));
        $this->assertSame(-9_000, $this->balance('4300'), 'the settlement discount, against revenue');
    }

    public function test_a_return_brings_goods_back_at_their_cost_and_is_used_as_credit(): void
    {
        $invoice = SalesInvoice::query()->create(['number' => 'INV-2', 'trans_date' => '2026-11-05', 'customer_id' => $this->customer->id, 'taxable' => true, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $invoice->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'quantity' => 4, 'unit_id' => $this->item->unit1_id, 'base_quantity' => 4, 'unit_price' => 150_000, 'tax_code_id' => $this->vat->id, 'warehouse_id' => $this->warehouse->id]);
        $invoice->refreshTotal();
        $this->docs->created($invoice);
        $this->assertSame('16.0000', StockQuery::onHand($this->item->id), 'a direct invoice takes the goods out');
        $this->assertSame(666_000, $invoice->fresh()->total);

        $return = SalesReturn::query()->create(['number' => 'SR-1', 'trans_date' => '2026-11-08', 'customer_id' => $this->customer->id, 'return_type' => 'invoice', 'source_type' => 'sales_invoice', 'source_id' => $invoice->id, 'taxable' => true, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $return->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'quantity' => 1, 'unit_id' => $this->item->unit1_id, 'base_quantity' => 1, 'unit_price' => 150_000, 'tax_code_id' => $this->vat->id, 'warehouse_id' => $this->warehouse->id]);
        $return->refreshTotal();
        $this->docs->created($return);

        $this->assertSame('17.0000', StockQuery::onHand($this->item->id));
        $this->assertSame(166_500, $return->fresh()->total);
        $this->assertSame(-150_000, $this->balance('4200'), 'sales returns, a debit against a revenue-normal account');
        $this->assertSame(666_000 - 166_500, $this->balance('1200'));
        $this->assertSame(400_000 - 100_000, $this->balance('5100'), 'cost of goods sold reversed at the cost the goods left with');

        $receipt = SalesReceipt::query()->create(['number' => 'CB-2', 'trans_date' => '2026-11-12', 'customer_id' => $this->customer->id, 'bank_account_id' => Account::query()->where('no', '1102')->value('id'), 'use_credit' => true, 'created_by' => auth()->id()]);
        $receipt->lines()->createMany([
            ['sort' => 0, 'receivable_type' => 'sales_invoice', 'receivable_id' => $invoice->id, 'amount' => 666_000, 'discount' => 0],
            ['sort' => 1, 'receivable_type' => 'sales_return', 'receivable_id' => $return->id, 'amount' => -166_500, 'discount' => 0],
        ]);
        $receipt->refreshTotal();
        $this->docs->created($receipt);

        $this->assertSame(499_500, $receipt->fresh()->amount);
        $this->assertSame(0, $this->balance('1200'));
        $this->assertSame('paid', $invoice->fresh()->payment_status);
        $this->assertSame('paid', $return->fresh()->payment_status);
    }

    public function test_a_down_payment_is_a_receivable_deducted_gross_on_the_invoice(): void
    {
        $dp = SalesDownPayment::query()->create(['number' => 'INV-DP-1', 'trans_date' => '2026-11-01', 'customer_id' => $this->customer->id, 'amount' => 300_000, 'taxable' => true, 'inclusive_tax' => false, 'tax_code_id' => $this->vat->id, 'created_by' => auth()->id()]);
        $dp->refreshTotal();
        $this->docs->created($dp);
        $this->assertSame(333_000, $dp->fresh()->total);
        $this->assertSame(333_000, $this->balance('1200'));
        $this->assertSame(300_000, $this->balance('2210'));

        $invoice = SalesInvoice::query()->create(['number' => 'INV-3', 'trans_date' => '2026-11-06', 'customer_id' => $this->customer->id, 'taxable' => true, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $invoice->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'quantity' => 10, 'unit_id' => $this->item->unit1_id, 'base_quantity' => 10, 'unit_price' => 150_000, 'tax_code_id' => $this->vat->id, 'warehouse_id' => $this->warehouse->id]);
        $invoice->downPayments()->create(['sales_down_payment_id' => $dp->id, 'amount' => 333_000]);
        $invoice->refreshTotal();
        $this->docs->created($invoice);
        $invoice->refresh();

        $this->assertSame(1_665_000, $invoice->total);
        $this->assertSame(333_000, $invoice->down_payment_total);
        $this->assertSame(1_332_000, $invoice->balance());
        $this->assertSame(0, $this->balance('2210'));
        $this->assertSame(165_000, $this->balance('2200'), 'VAT out not counted twice');
        $this->assertSame('processed', $dp->fresh()->status);
    }

    public function test_the_price_resolver_prefers_the_adjustment_then_the_category_price_then_the_item(): void
    {
        $category = PriceCategory::query()->where('is_default', true)->firstOrFail();
        $ctn = Unit::query()->where('name', 'CTN')->firstOrFail();
        $this->item->units()->create(['unit_id' => $ctn->id, 'ratio' => 12, 'sell_price' => 0]);

        $this->assertSame('150000.0000', PriceResolver::resolve($this->customer, $this->item->fresh(), null)['price']);
        $this->assertSame('1800000.0000', PriceResolver::resolve($this->customer, $this->item->fresh(), $ctn->id)['price'], 'the base price scaled to the carton');

        $this->item->prices()->create(['price_category_id' => $category->id, 'price' => 140_000]);
        $this->assertSame('140000', PriceResolver::resolve($this->customer, $this->item->fresh(), null)['price']);

        $adjustment = SellingPriceAdjustment::query()->create(['number' => 'PA-1', 'price_category_id' => $category->id, 'sales_adjustment_type' => 'price', 'trans_date' => '2026-11-10']);
        $adjustment->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'value' => 135_000]);
        $this->assertSame('140000', PriceResolver::resolve($this->customer, $this->item->fresh(), null, '2026-11-09')['price'], 'not yet in force');
        $resolved = PriceResolver::resolve($this->customer, $this->item->fresh(), null, '2026-11-15');
        $this->assertSame('135000.0000', $resolved['price']);
        $this->assertStringContainsString('PA-1', $resolved['source']);
    }

    public function test_approval_needs_the_right_and_another_person_and_respects_the_credit_limit(): void
    {
        app(Preferensi::class)->set(PreferensiKey::MarketingApprovalRequired, true);
        $sales = User::factory()->create();
        $marketing = User::factory()->create();
        AccessGroup::query()->where('name', 'Sales')->firstOrFail()->users()->attach($sales);
        AccessGroup::query()->where('name', 'Marketing')->firstOrFail()->users()->attach($marketing);
        $this->actingAs($sales);

        $order = $this->order(10, 150_000);
        $this->assertSame('awaiting', $order->approval_status);

        $approval = app(OrderApproval::class);
        try {
            $approval->approve($order, $sales);
            $this->fail('sales cannot approve');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('right', $e->getMessage());
        }

        $this->customer->update(['credit_limit_amount_enabled' => true, 'credit_limit_amount' => 1_000_000]);
        $this->actingAs($marketing);
        try {
            $approval->approve($order->fresh(), $marketing);
            $this->fail('over the credit limit');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('credit limit', $e->getMessage());
        }

        $this->customer->update(['credit_limit_amount' => 2_000_000]);
        $approval->approve($order->fresh(), $marketing);
        $this->assertSame('approved', $order->fresh()->approval_status);
        $this->assertSame($marketing->id, $order->fresh()->approved_by);
        $this->assertSame(1_665_000, app(CreditCheck::class)->openOrders($this->customer));
    }

    public function test_an_invoice_unpaid_for_over_150_days_freezes_the_customer(): void
    {
        $invoice = SalesInvoice::query()->create(['number' => 'INV-OLD', 'trans_date' => '2026-05-01', 'customer_id' => $this->customer->id, 'taxable' => false, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $invoice->lines()->create(['sort' => 0, 'item_id' => $this->item->id, 'quantity' => 1, 'unit_id' => $this->item->unit1_id, 'base_quantity' => 1, 'unit_price' => 150_000, 'warehouse_id' => $this->warehouse->id]);
        $invoice->refreshTotal();
        $this->docs->created($invoice);

        $check = app(CreditCheck::class);
        $this->assertTrue($check->needsNotice($this->customer));
        try {
            $check->assert($this->customer, 100);
            $this->fail('frozen');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('frozen', $e->getMessage());
        }

        $receipt = SalesReceipt::query()->create(['number' => 'CB-9', 'trans_date' => '2026-11-14', 'customer_id' => $this->customer->id, 'bank_account_id' => Account::query()->where('no', '1102')->value('id'), 'created_by' => auth()->id()]);
        $receipt->lines()->create(['sort' => 0, 'receivable_type' => 'sales_invoice', 'receivable_id' => $invoice->id, 'amount' => 150_000, 'discount' => 0]);
        $receipt->refreshTotal();
        $this->docs->created($receipt);

        $check->assert($this->customer, 100);
        $this->assertFalse($check->needsNotice($this->customer), 'lifted the moment the aged invoice is settled');
    }
}
