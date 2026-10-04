<?php

namespace Tests\Feature\Domain;

use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Domain\Posting\DocumentRepository;
use App\Domain\Tax\FilingDocuments;
use App\Domain\Tax\TaxFilingService;
use App\Models\Company\PaymentTerm;
use App\Models\Company\TaxCode;
use App\Models\Inventory\InventoryAdjustment;
use App\Models\Inventory\Item;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\Customer;
use App\Models\Sales\PriceCategory;
use App\Models\Sales\SalesInvoice;
use App\Models\Tax\TaxFiling;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaxFilingTest extends TestCase
{
    private SalesInvoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-30 09:00:00');
        CarbonImmutable::setTestNow('2026-11-30 09:00:00');
        Storage::fake('local');
        $this->seed();
        $this->actingAsAdmin();
        app(Preferensi::class)->setMany([
            PreferensiKey::CompanyNpwp->value => '01.234.567.8-901.000',
            PreferensiKey::TaxCompanyName->value => 'PT August Parts',
        ]);

        $customer = Customer::query()->create(['number' => 'C-00001', 'name' => 'Bengkel Maju', 'wp_type' => 'npwp', 'wp_number' => '09.876.543.2-109.000', 'wp_name' => 'PT Maju Jaya', 'bill_street' => 'Jl. Raya 1', 'bill_city' => 'Jakarta', 'bill_zip_code' => '12345', 'price_category_id' => PriceCategory::query()->where('is_default', true)->value('id'), 'payment_term_id' => PaymentTerm::default()->id]);
        $item = Item::query()->create(['number' => 'ITM-00001', 'name' => 'Brake pad', 'unit1_id' => Unit::query()->where('name', 'PCS')->value('id'), 'sell_price' => 150_000, 'purchase_price' => 100_000, 'item_tax_code' => '270111']);
        $docs = app(DocumentRepository::class);
        $opening = InventoryAdjustment::query()->create(['number' => 'ADJ-OPEN', 'trans_date' => '2026-10-01', 'created_by' => auth()->id()]);
        $opening->lines()->create(['sort' => 0, 'item_id' => $item->id, 'adjustment_type' => 'quantity', 'quantity' => 20, 'unit_id' => $item->unit1_id, 'base_quantity' => 20, 'unit_cost' => 100_000, 'total_cost' => 0, 'warehouse_id' => Warehouse::default()->id]);
        $docs->created($opening);

        $this->invoice = SalesInvoice::query()->create(['number' => 'INV-2611-0001', 'trans_date' => '2026-11-05', 'customer_id' => $customer->id, 'taxable' => true, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $this->invoice->lines()->create(['sort' => 0, 'item_id' => $item->id, 'quantity' => 10, 'unit_id' => $item->unit1_id, 'base_quantity' => 10, 'unit_price' => 150_000, 'tax_code_id' => TaxCode::default()->id, 'warehouse_id' => Warehouse::default()->id]);
        $this->invoice->refreshTotal();
        $docs->created($this->invoice);
        $this->invoice->refresh();
    }

    public function test_the_period_lists_taxable_invoices_and_the_coretax_file_carries_the_11_12_tax_base(): void
    {
        $this->assertSame(1_500_000, $this->invoice->subtotal);
        $this->assertSame(1_375_000, $this->invoice->dpp_total, '11/12 of the price');
        $this->assertSame(165_000, $this->invoice->tax_total, '12 % of the other tax base: an 11 % burden');

        $listed = FilingDocuments::query(TaxFiling::OUT, '2026-11-01', '2026-11-30');
        $this->assertSame(['INV-2611-0001'], $listed->pluck('number')->all());
        $this->assertSame('draft', FilingDocuments::status($this->invoice));
        $this->assertCount(0, FilingDocuments::query(TaxFiling::OUT, '2026-12-01', '2026-12-31')->get());

        $filing = app(TaxFilingService::class)->export($listed->get(), TaxFiling::CORETAX, 2026, 11);
        $this->assertSame(1, $filing->document_count);
        $this->assertSame(1_375_000, $filing->dpp_total);
        $this->assertSame(165_000, $filing->tax_total);
        Storage::disk('local')->assertExists($filing->file_path);
        $xml = Storage::disk('local')->get($filing->file_path);
        $this->assertStringContainsString('<TaxInvoiceBulk', $xml);
        $this->assertStringContainsString('<TIN>012345678901000</TIN>', $xml);
        $this->assertStringContainsString('<TrxCode>04</TrxCode>', $xml, 'the 11/12 base means transaction code 04');
        $this->assertStringContainsString('<BuyerTin>098765432109000</BuyerTin>', $xml);
        $this->assertStringContainsString('<BuyerName>PT Maju Jaya</BuyerName>', $xml);
        $this->assertStringContainsString('<BuyerAdress>Jl. Raya 1, Jakarta, 12345</BuyerAdress>', $xml);
        $this->assertStringContainsString('<Code>270111</Code>', $xml);
        $this->assertStringContainsString('<Unit>UM.0018</Unit>', $xml);
        $this->assertStringContainsString('<Price>150000</Price>', $xml);
        $this->assertStringContainsString('<Qty>10</Qty>', $xml);
        $this->assertStringContainsString('<TaxBase>1500000</TaxBase>', $xml);
        $this->assertStringContainsString('<OtherTaxBase>1375000</OtherTaxBase>', $xml);
        $this->assertStringContainsString('<VAT>165000</VAT>', $xml);
        $this->assertTrue(simplexml_load_string($xml) !== false, 'well-formed');
        $this->assertSame('exported', FilingDocuments::status($this->invoice));

        $result = app(TaxFilingService::class)->storeSerials("INV-2611-0001\t010.002-26.00000123\nINV-NOPE, 010.002-26.00000124\n");
        $this->assertSame(['INV-2611-0001 → 010.002-26.00000123'], $result['stored']);
        $this->assertSame(['INV-NOPE, 010.002-26.00000124'], $result['unknown']);
        $this->assertSame('010.002-26.00000123', $this->invoice->fresh()->nsfp);
        $this->assertNotNull($this->invoice->fresh()->nsfp_filed_at);
        $this->assertSame('numbered', FilingDocuments::status($this->invoice->fresh()));
    }

    public function test_the_legacy_csv_reproduces_the_older_layout(): void
    {
        $this->invoice->forceFill(['nsfp' => '010.002-26.00000123'])->saveQuietly();
        $filing = app(TaxFilingService::class)->export(FilingDocuments::query(TaxFiling::OUT, '2026-11-01', '2026-11-30')->get(), TaxFiling::LEGACY, 2026, 11);
        $csv = Storage::disk('local')->get($filing->file_path);
        $lines = array_values(array_filter(explode("\n", $csv)));
        $this->assertStringStartsWith('FK,KD_JENIS_TRANSAKSI,FG_PENGGANTI,NOMOR_FAKTUR', $lines[0]);
        $this->assertStringStartsWith('LT,NPWP', $lines[1]);
        $this->assertStringStartsWith('OF,KODE_OBJEK', $lines[2]);
        $this->assertSame('FK,01,0,0100022600000123,11,2026,05/11/2026,098765432109000,"PT Maju Jaya","Jl. Raya 1, Jakarta, 12345",1375000,165000,0,,0,0,0,0,INV-2611-0001', $lines[3]);
        $this->assertStringStartsWith('LT,098765432109000,"PT Maju Jaya"', $lines[4]);
        $this->assertSame('OF,270111,"Brake pad",150000,10,1500000,0,1375000,165000,0,0', $lines[5]);
        $this->assertStringEndsWith('.csv', $filing->file_name);
    }

    public function test_an_empty_selection_is_refused(): void
    {
        $this->expectException(\RuntimeException::class);
        app(TaxFilingService::class)->export(SalesInvoice::query()->whereRaw('1 = 0')->get(), TaxFiling::CORETAX, 2026, 11);
    }
}
