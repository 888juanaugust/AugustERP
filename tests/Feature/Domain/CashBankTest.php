<?php

namespace Tests\Feature\Domain;

use App\Domain\CashBank\GiroService;
use App\Domain\CashBank\Reconciler;
use App\Domain\CashBank\StatementImporter;
use App\Domain\Documents\PaymentMethod;
use App\Domain\Posting\AccountBalances;
use App\Domain\Posting\DocumentRepository;
use App\Domain\Posting\Exceptions\DocumentLockedException;
use App\Models\CashBank\BankTransfer;
use App\Models\CashBank\CashPayment;
use App\Models\CashBank\CashReceipt;
use App\Models\CashBank\Giro;
use App\Models\Company\PaymentTerm;
use App\Models\GeneralLedger\Account;
use App\Models\GeneralLedger\JournalLine;
use App\Models\GeneralLedger\Posting;
use App\Models\Inventory\InventoryAdjustment;
use App\Models\Inventory\Item;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\Customer;
use App\Models\Sales\PriceCategory;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesReceipt;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CashBankTest extends TestCase
{
    private DocumentRepository $docs;

    private int $bank;

    private int $cash;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-30 09:00:00');
        CarbonImmutable::setTestNow('2026-11-30 09:00:00');
        $this->seed();
        $this->actingAsAdmin();
        $this->docs = app(DocumentRepository::class);
        $this->bank = $this->account('1102');
        $this->cash = $this->account('1101');
    }

    private function account(string $no): int
    {
        return (int) Account::query()->where('no', $no)->value('id');
    }

    private function balance(string $no, ?string $asOf = null): int
    {
        return AccountBalances::asOf($asOf)[$this->account($no)] ?? 0;
    }

    private function receipt(string $number, string $date, int $amount, string $creditTo, ?string $chequeNo = null, ?string $chequeDate = null): CashReceipt
    {
        $receipt = CashReceipt::query()->create(['number' => $number, 'trans_date' => $date, 'bank_account_id' => $this->bank, 'cheque_no' => $chequeNo, 'cheque_date' => $chequeDate, 'payer' => 'Owner', 'created_by' => auth()->id()]);
        $receipt->lines()->create(['sort' => 0, 'account_id' => $this->account($creditTo), 'amount' => $amount, 'memo' => 'Paid in']);
        $receipt->refreshTotal();
        $this->docs->created($receipt);

        return $receipt->fresh();
    }

    public function test_payments_and_receipts_post_each_line_against_the_bank(): void
    {
        $this->receipt('CR-1', '2026-11-01', 20_000_000, '3100');
        $this->assertSame(20_000_000, $this->balance('1102'));
        $this->assertSame(20_000_000, $this->balance('3100'));

        $payment = CashPayment::query()->create(['number' => 'CP-1', 'trans_date' => '2026-11-02', 'bank_account_id' => $this->bank, 'payee' => 'Landlord', 'created_by' => auth()->id()]);
        $payment->lines()->createMany([
            ['sort' => 0, 'account_id' => $this->account('6200'), 'amount' => 5_000_000, 'memo' => 'November rent'],
            ['sort' => 1, 'account_id' => $this->account('6500'), 'amount' => 250_000, 'memo' => 'Stamps'],
        ]);
        $payment->refreshTotal();
        $this->docs->created($payment);

        $this->assertSame(5_250_000, $payment->fresh()->amount);
        $this->assertSame(20_000_000 - 5_250_000, $this->balance('1102'));
        $this->assertSame(5_000_000, $this->balance('6200'));
        $this->assertSame(250_000, $this->balance('6500'));
        $this->assertNull($payment->fresh()->giro, 'no cheque number, no giro');
    }

    public function test_a_bank_transfer_moves_the_money_and_charges_the_fee_to_the_side_asked(): void
    {
        $this->receipt('CR-1', '2026-11-01', 20_000_000, '3100');

        $transfer = BankTransfer::query()->create(['number' => 'BT-1', 'trans_date' => '2026-11-05', 'from_bank_account_id' => $this->bank, 'to_bank_account_id' => $this->cash, 'amount' => 10_000_000, 'created_by' => auth()->id()]);
        $transfer->fees()->create(['sort' => 0, 'account_id' => $this->account('8100'), 'charged_to' => 'from', 'amount' => 6_500, 'memo' => 'Transfer fee']);
        $transfer->refreshTotal();
        $this->docs->created($transfer);

        $this->assertSame(6_500, $transfer->fresh()->fees_total);
        $this->assertSame(20_000_000 - 10_000_000 - 6_500, $this->balance('1102'));
        $this->assertSame(10_000_000, $this->balance('1101'));
        $this->assertSame(6_500, $this->balance('8100'));
    }

    public function test_the_statement_imports_matches_the_book_and_a_reconciled_line_locks_its_document(): void
    {
        $receipt = $this->receipt('CR-1', '2026-11-01', 20_000_000, '3100');
        $transfer = BankTransfer::query()->create(['number' => 'BT-1', 'trans_date' => '2026-11-05', 'from_bank_account_id' => $this->bank, 'to_bank_account_id' => $this->cash, 'amount' => 10_000_000, 'created_by' => auth()->id()]);
        $transfer->fees()->create(['sort' => 0, 'account_id' => $this->account('8100'), 'charged_to' => 'from', 'amount' => 6_500]);
        $transfer->refreshTotal();
        $this->docs->created($transfer);

        $csv = tempnam(sys_get_temp_dir(), 'stmt').'.csv';
        file_put_contents($csv, implode("\n", [
            'Rekening Koran BANK',
            'Tanggal;Keterangan;Debet;Kredit;Saldo',
            '01/11/2026;SETORAN MODAL;;20.000.000,00;20.000.000,00',
            '06/11/2026;BIAYA ADM TRANSFER;6.500,00;;19.993.500,00',
            '06/11/2026;TRF KE KAS KECIL;10.000.000,00;;9.993.500,00',
            ';;;;',
        ]));
        $statement = app(StatementImporter::class)->import($this->bank, $csv, 'nov.csv');
        $this->assertSame(3, $statement->line_count);
        $this->assertSame([20_000_000, -6_500, -10_000_000], $statement->lines->pluck('amount')->all(), 'money in positive, money out negative');
        $this->assertSame('2026-11-01', $statement->from_date->toDateString());
        $this->assertSame(9_993_500, $statement->lines->last()->balance);

        $reconciler = app(Reconciler::class);
        $rec = $reconciler->open($this->bank, '2026-11-01', '2026-11-30', 9_993_500);
        $this->assertCount(3, $reconciler->bookLines($rec));
        $this->assertSame(['book_balance' => 9_993_500, 'cleared_balance' => 0, 'uncleared' => 9_993_500, 'statement_balance' => 9_993_500, 'difference' => 9_993_500], $reconciler->summary($rec));

        try {
            $reconciler->close($rec);
            $this->fail('nothing cleared yet');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('differs', $e->getMessage());
        }

        $this->assertSame(3, $reconciler->autoMatch($rec), 'every book line is explained by a statement line a day later');
        $this->assertSame(0, $reconciler->summary($rec)['difference']);
        $this->assertSame(3, $statement->lines()->whereHas('reconciliationItem')->count());
        $reconciler->close($rec);
        $this->assertTrue($rec->fresh()->isClosed());

        // The receipt's bank line is cleared: the receipt is history now.
        try {
            $this->docs->beforeUpdate($receipt->fresh());
            $this->fail('reconciled');
        } catch (DocumentLockedException $e) {
            $this->assertStringContainsString('reconciled', $e->getMessage());
        }
        try {
            $this->docs->delete($transfer->fresh());
            $this->fail('reconciled');
        } catch (DocumentLockedException $e) {
            $this->assertStringContainsString('reconciled', $e->getMessage());
        }
        $this->assertSame(1, Posting::active()->where('posting_key', $transfer->postingKey())->count());

        $later = $reconciler->open($this->bank, '2026-12-01', '2026-12-31', 9_993_500);
        $this->assertCount(0, $reconciler->bookLines($later), 'cleared lines do not come back in the next period');
    }

    public function test_a_giro_received_waits_in_giros_receivable_until_it_clears_and_bounces_back_to_the_invoice(): void
    {
        $customer = Customer::query()->create(['number' => 'C-00001', 'name' => 'Bengkel Maju', 'price_category_id' => PriceCategory::query()->where('is_default', true)->value('id'), 'payment_term_id' => PaymentTerm::default()->id]);
        $item = Item::query()->create(['number' => 'ITM-00001', 'name' => 'Brake pad', 'unit1_id' => Unit::query()->where('name', 'PCS')->value('id'), 'sell_price' => 150_000, 'purchase_price' => 100_000]);
        $opening = InventoryAdjustment::query()->create(['number' => 'ADJ-OPEN', 'trans_date' => '2026-10-01', 'created_by' => auth()->id()]);
        $opening->lines()->create(['sort' => 0, 'item_id' => $item->id, 'adjustment_type' => 'quantity', 'quantity' => 20, 'unit_id' => $item->unit1_id, 'base_quantity' => 20, 'unit_cost' => 100_000, 'total_cost' => 0, 'warehouse_id' => Warehouse::default()->id]);
        $this->docs->created($opening);

        $invoice = fn (string $number, string $date) => tap(SalesInvoice::query()->create(['number' => $number, 'trans_date' => $date, 'customer_id' => $customer->id, 'taxable' => false, 'inclusive_tax' => false, 'created_by' => auth()->id()]), function (SalesInvoice $inv) use ($item): void {
            $inv->lines()->create(['sort' => 0, 'item_id' => $item->id, 'quantity' => 1, 'unit_id' => $item->unit1_id, 'base_quantity' => 1, 'unit_price' => 150_000, 'warehouse_id' => Warehouse::default()->id]);
            $inv->refreshTotal();
            $this->docs->created($inv);
        });
        $receipt = function (string $number, string $date, SalesInvoice $inv, string $chequeNo) use ($customer): SalesReceipt {
            $r = SalesReceipt::query()->create(['number' => $number, 'trans_date' => $date, 'customer_id' => $customer->id, 'bank_account_id' => $this->bank, 'payment_method' => PaymentMethod::Cheque, 'cheque_no' => $chequeNo, 'cheque_date' => '2026-11-20', 'created_by' => auth()->id()]);
            $r->lines()->create(['sort' => 0, 'receivable_type' => 'sales_invoice', 'receivable_id' => $inv->id, 'amount' => 150_000, 'discount' => 0]);
            $r->refreshTotal();
            $this->docs->created($r);

            return $r->fresh();
        };

        $inv1 = $invoice('INV-1', '2026-11-05');
        $r1 = $receipt('CB-1', '2026-11-06', $inv1, 'BG-123');

        $giro = $r1->giro;
        $this->assertNotNull($giro);
        $this->assertSame(Giro::OUTSTANDING, $giro->status);
        $this->assertSame(Giro::IN, $giro->direction);
        $this->assertSame('BG-123', $giro->number);
        $this->assertSame('2026-11-20', $giro->due_date->toDateString());
        $this->assertSame('paid', $inv1->fresh()->payment_status, 'the invoice is settled by the receipt');
        $this->assertSame(150_000, $this->balance('1105'), 'in giros receivable, not in the bank');
        $this->assertSame(0, $this->balance('1102'));

        $giros = app(GiroService::class);
        $giros->clear($giro, '2026-11-20');
        $this->assertSame(0, $this->balance('1105'));
        $this->assertSame(150_000, $this->balance('1102'));
        $this->assertSame(0, $this->balance('1102', '2026-11-19'), 'the bank leg is dated the day the giro cleared');
        $this->assertSame(Giro::CLEARED, $giro->fresh()->status);
        try {
            $this->docs->beforeUpdate($r1->fresh());
            $this->fail('cleared giro');
        } catch (DocumentLockedException $e) {
            $this->assertStringContainsString('giro BG-123 cleared', $e->getMessage());
        }

        $inv2 = $invoice('INV-2', '2026-11-07');
        $r2 = $receipt('CB-2', '2026-11-08', $inv2, 'BG-124');
        $this->assertSame('paid', $inv2->fresh()->payment_status);
        $this->assertSame(150_000, $this->balance('1105'));

        $giros->bounce($r2->giro, '2026-11-25', 'Insufficient funds');
        $this->assertSame(Giro::BOUNCED, $r2->giro->fresh()->status);
        $this->assertSame('unpaid', $inv2->fresh()->payment_status, 'a bounced giro paid nothing: the invoice is open again');
        $this->assertSame(0, $this->balance('1105'));
        $this->assertSame(0, Posting::active()->where('posting_key', $r2->postingKey())->count(), 'the receipt no longer posts');
        $this->assertSame(0, JournalLine::query()->active()->where('account_id', $this->account('1200'))->where('credit', '>', 0)->where('posting_id', '!=', $r1->posting->id)->count());
    }

    public function test_a_payment_by_giro_waits_in_giros_payable_until_it_clears(): void
    {
        $this->receipt('CR-1', '2026-11-01', 20_000_000, '3100');
        $payment = CashPayment::query()->create(['number' => 'CP-1', 'trans_date' => '2026-11-02', 'bank_account_id' => $this->bank, 'cheque_no' => 'CK-9', 'cheque_date' => '2026-11-15', 'payee' => 'Landlord', 'created_by' => auth()->id()]);
        $payment->lines()->create(['sort' => 0, 'account_id' => $this->account('6200'), 'amount' => 5_000_000, 'memo' => 'Rent']);
        $payment->refreshTotal();
        $this->docs->created($payment);

        $this->assertSame(Giro::OUT, $payment->fresh()->giro->direction);
        $this->assertSame(5_000_000, $this->balance('6200'));
        $this->assertSame(5_000_000, $this->balance('2105'), 'giros payable, read on its credit side');
        $this->assertSame(20_000_000, $this->balance('1102'), 'the bank is untouched until the giro clears');

        app(GiroService::class)->clear($payment->fresh()->giro, '2026-11-15');
        $this->assertSame(0, $this->balance('2105'));
        $this->assertSame(15_000_000, $this->balance('1102'));

        // A giro received on an ordinary receipt and then removed by editing the document is dropped from the register.
        $receipt = $this->receipt('CR-2', '2026-11-03', 1_000_000, '7100', 'BG-77', '2026-11-30');
        $this->assertSame(1_000_000, $this->balance('1105'));
        $before = $this->docs->beforeUpdate($receipt);
        $receipt->forceFill(['cheque_no' => null])->save();
        $this->docs->updated($receipt, $before);
        $this->assertNull($receipt->fresh()->giro);
        $this->assertSame(0, $this->balance('1105'));
        $this->assertSame(16_000_000, $this->balance('1102'));
    }
}
