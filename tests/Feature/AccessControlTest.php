<?php

namespace Tests\Feature;

use App\Domain\Access\AccessWindow;
use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Access\MenuKey;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Domain\Posting\DocumentRepository;
use App\Domain\Posting\Exceptions\DocumentLockedException;
use App\Domain\Printing\PrintJob;
use App\Domain\Tax\TaxFilingService;
use App\Filament\Pages\Reports\TrialBalance;
use App\Filament\Resources\GeneralLedger\JournalVouchers\JournalVoucherResource;
use App\Filament\Resources\Sales\Customers\Pages\EditCustomer;
use App\Filament\Resources\Settings\AccessGroups\AccessGroupResource;
use App\Filament\Resources\Settings\Users\Pages\EditUser;
use App\Filament\Support\BranchFields;
use App\Models\Company\AuditLog;
use App\Models\Company\Branch;
use App\Models\GeneralLedger\Account;
use App\Models\GeneralLedger\JournalVoucher;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\SalesInvoice;
use App\Models\Settings\AccessGroup;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/** The special rights, branch limits and the access window are enforced, not only stored. */
class AccessControlTest extends TestCase
{
    private DocumentRepository $docs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $this->seed();
        $this->docs = app(DocumentRepository::class);
    }

    /** An operator in the named seeded groups. */
    private function operator(string ...$groups): User
    {
        $user = User::factory()->create();
        foreach ($groups as $group) {
            AccessGroup::query()->where('name', $group)->firstOrFail()->users()->attach($user);
        }
        $this->actingAs($user);
        $this->freshRequest();

        return $user;
    }

    private function voucher(string $date, ?int $branchId = null, string $number = 'JV-1'): JournalVoucher
    {
        $voucher = JournalVoucher::query()->create(['number' => $number, 'trans_date' => $date, 'branch_id' => $branchId, 'description' => 'Test', 'created_by' => auth()->id()]);
        $voucher->lines()->createMany([
            ['sort' => 0, 'account_id' => Account::query()->where('no', '6100')->value('id'), 'debit' => 10_000, 'credit' => 0],
            ['sort' => 1, 'account_id' => Account::query()->where('no', '1101')->value('id'), 'debit' => 0, 'credit' => 10_000],
        ]);

        return $voucher;
    }

    public function test_back_dating_takes_the_right_but_editing_a_past_document_on_its_own_date_does_not(): void
    {
        $this->operator('Finance');
        $this->docs->created($this->voucher('2026-11-16', number: 'JV-TODAY'));

        try {
            $this->docs->created($this->voucher('2026-11-10', number: 'JV-PAST'));
            $this->fail('an operator without the right cannot date a transaction before today');
        } catch (DocumentLockedException $e) {
            $this->assertStringContainsString('back-date', $e->getMessage());
        }

        $accountant = $this->operator('Accounting');
        $past = $this->voucher('2026-11-10', number: 'JV-PAST-2');
        $this->docs->created($past);
        $this->assertTrue(app(HakAkses::class)->allowsSpecial($accountant, HakKhusus::BackdateTransactions));

        $this->operator('Finance');
        $past->forceFill(['created_by' => auth()->id()])->saveQuietly();
        $this->docs->beforeUpdate($past->fresh(), Carbon::parse('2026-11-10'));
        $this->expectException(DocumentLockedException::class);
        $this->docs->beforeUpdate($past->fresh(), Carbon::parse('2026-11-09'));
    }

    public function test_deleting_a_posted_transaction_takes_the_right(): void
    {
        $this->operator('Finance', 'Accounting');
        $voucher = $this->voucher('2026-11-16');
        $this->docs->created($voucher);

        $this->operator('Finance');
        $voucher->forceFill(['created_by' => auth()->id()])->saveQuietly();
        try {
            $this->docs->delete($voucher->fresh());
            $this->fail('deleting a posted voucher takes the right');
        } catch (DocumentLockedException $e) {
            $this->assertStringContainsString('delete posted transactions', $e->getMessage());
        }

        $this->operator('Accounting');
        $voucher->forceFill(['created_by' => auth()->id()])->saveQuietly();
        $this->docs->delete($voucher->fresh());
        $this->assertNull(JournalVoucher::query()->find($voucher->id));
    }

    public function test_credit_data_and_export_are_hidden_without_their_rights(): void
    {
        $customer = $this->sampleCustomer(['credit_limit_amount_enabled' => true, 'credit_limit_amount' => 5_000_000]);

        $this->operator('Sales');
        Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])
            ->assertFormFieldHidden('credit_limit_amount')
            ->assertFormFieldHidden('credit_limit_mode')
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame(5_000_000, (int) $customer->fresh()->credit_limit_amount, 'a hidden field keeps its value');

        $this->operator('Sales', 'Finance');
        Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])->assertFormFieldVisible('credit_limit_mode');

        $readers = AccessGroup::query()->create(['name' => 'Report readers']);
        $readers->syncRights([MenuKey::ReportCatalogue->value => ['view']]);
        $this->operator('Report readers');
        Livewire::test(TrialBalance::class)->assertActionHidden('export');

        $readers->syncSpecialRights([HakKhusus::ExportData->value]);
        $this->freshRequest();
        Livewire::test(TrialBalance::class)->assertActionVisible('export');
    }

    public function test_only_an_administrator_grants_administrator_or_changes_groups(): void
    {
        $admin = $this->actingAsAdmin();
        $managers = AccessGroup::query()->create(['name' => 'User managers']);
        $managers->syncRights([MenuKey::Users->value => ['view', 'create', 'update'], MenuKey::AccessGroups->value => ['view', 'create', 'update', 'delete']]);
        $manager = $this->operator('User managers');

        $refused = function (callable $change, string $message): void {
            try {
                $change();
                $this->fail("refused: {$message}");
            } catch (ValidationException $e) {
                $this->assertStringContainsString($message, implode(' ', Arr::flatten($e->errors())));
            }
        };
        $refused(fn () => $manager->forceFill(['access_type' => 'administrator'])->save(), 'Only an administrator makes');
        $refused(fn () => User::factory()->create(['access_type' => 'administrator']), 'Only an administrator makes');
        $refused(fn () => $admin->forceFill(['password' => 'taken-over-123'])->save(), 'Only an administrator changes');
        $this->assertFalse($manager->fresh()->isAdministrator());

        // Through the screen the access type and groups are locked, and a changed value is not saved.
        Livewire::test(EditUser::class, ['record' => $manager->getRouteKey()])
            ->assertFormFieldIsDisabled('access_type')
            ->assertFormFieldIsDisabled('accessGroups')
            ->set('data.access_type', 'administrator')
            ->call('save');
        $this->assertFalse($manager->fresh()->isAdministrator());
        $this->assertFalse(AccessGroupResource::canEdit($managers));
        $this->assertFalse(AccessGroupResource::canCreate());

        // The last administrator is never demoted, even by themselves.
        $this->actingAs($admin);
        User::query()->where('access_type', 'administrator')->whereKeyNot($admin->id)->update(['access_type' => 'operator']);
        $refused(fn () => $admin->forceFill(['access_type' => 'operator'])->save(), 'only active administrator');
    }

    public function test_the_print_page_needs_sign_in_a_signed_link_and_the_branch(): void
    {
        $head = Branch::default();
        $east = Branch::query()->create(['name' => 'East', 'used_all_user' => false]);
        $this->actingAsAdmin();
        $mine = $this->voucher('2026-11-16', $head->id, 'JV-HEAD');
        $this->docs->created($mine);
        $theirs = $this->voucher('2026-11-16', $east->id, 'JV-EAST');
        $this->docs->created($theirs);

        $this->operator('Accounting');
        $this->get(PrintJob::url($mine))->assertOk()->assertSee('JV-HEAD');
        $this->get(PrintJob::url($theirs))->assertNotFound();
        $this->get(route('filament.admin.print', ['alias' => 'journal_voucher', 'id' => $mine->id]))->assertForbidden();

        auth()->logout();
        $this->freshRequest();
        $this->get(PrintJob::url($mine))->assertRedirect();
    }

    public function test_a_user_limited_to_some_branches_sees_and_books_only_those(): void
    {
        $head = Branch::default();
        $east = Branch::query()->create(['name' => 'East', 'used_all_user' => false]);
        $this->actingAsAdmin();
        $this->voucher('2026-11-16', $head->id, 'JV-HEAD')->save();
        $this->voucher('2026-11-16', $east->id, 'JV-EAST')->save();
        $this->voucher('2026-11-16', null, 'JV-NONE')->save();
        $this->assertSame(['JV-EAST', 'JV-HEAD', 'JV-NONE'], JournalVoucherResource::getEloquentQuery()->orderBy('number')->pluck('number')->all(), 'an administrator sees every branch');
        $this->assertNull(BranchFields::reportBranch(null), 'and reports on all of them');

        $clerk = $this->operator('Accounting');
        $this->assertSame([$head->id], Branch::limitsOf($clerk));
        $this->assertSame(['JV-HEAD', 'JV-NONE'], JournalVoucherResource::getEloquentQuery()->orderBy('number')->pluck('number')->all());
        $this->assertSame($head->id, BranchFields::reportBranch(null), 'a limited user reports on one of their branches');
        $this->assertSame($head->id, BranchFields::reportBranch($east->id), 'never on another');

        try {
            $this->docs->created($this->voucher('2026-11-16', $east->id, 'JV-EAST-2'));
            $this->fail('booking in a branch the user is not assigned to');
        } catch (DocumentLockedException $e) {
            $this->assertStringContainsString('not assigned', $e->getMessage());
        }

        $east->users()->attach($clerk);
        $this->assertNull(Branch::limitsOf($clerk), 'assigned to every closed branch, the user is not limited');
        $this->docs->created($this->voucher('2026-11-16', $east->id, 'JV-EAST-3'));
        $this->assertNotNull(Warehouse::default());
    }

    public function test_the_access_window_turns_operators_away_outside_their_hours(): void
    {
        $preferensi = app(Preferensi::class);
        $preferensi->set(PreferensiKey::AccessRestriction, 'time_window');
        $preferensi->set(PreferensiKey::AccessFrom, '08:00');
        $preferensi->set(PreferensiKey::AccessUntil, '17:00');
        $window = app(AccessWindow::class);

        $admin = $this->actingAsAdmin();
        $clerk = $this->operator('Finance');
        $this->assertTrue($window->allows($clerk, Carbon::parse('2026-11-16 09:00')));
        $this->assertFalse($window->allows($clerk, Carbon::parse('2026-11-16 17:00')), 'the window closes at its end');
        $this->assertTrue($window->allows($admin, Carbon::parse('2026-11-16 23:00')), 'an administrator is never turned away');

        $night = AccessGroup::query()->create(['name' => 'Night shift', 'restriction_type' => 'time_window', 'restricted_from' => '22:00', 'restricted_until' => '02:00']);
        $night->users()->attach($clerk);
        $this->assertTrue($window->allows($clerk, Carbon::parse('2026-11-16 23:30')), 'any group allowing the moment is enough; the window runs past midnight');
        $this->assertTrue($window->allows($clerk, Carbon::parse('2026-11-17 01:30')));
        $this->assertFalse($window->allows($clerk, Carbon::parse('2026-11-17 03:00')));

        $this->travelTo(Carbon::parse('2026-11-17 03:00'));
        $this->get('/admin/dashboard')->assertForbidden()->assertSee('08:00–17:00');
        $this->travelTo(Carbon::parse('2026-11-17 09:00'));
        $this->get('/admin/dashboard')->assertOk();

        $preferensi->set(PreferensiKey::AccessRestriction, 'all');
        $night->users()->detach($clerk);
        $this->assertFalse($window->allows($clerk, Carbon::parse('2026-11-17 09:00')), 'restricted for everyone');
    }

    public function test_an_invoice_with_a_tax_serial_is_locked_until_the_serial_is_cleared(): void
    {
        $this->actingAsAdmin();
        $customer = $this->sampleCustomer();
        $item = $this->sampleItem(['item_type' => 'service']);
        $invoice = SalesInvoice::query()->create(['number' => 'INV-1', 'trans_date' => '2026-11-16', 'customer_id' => $customer->id, 'taxable' => false, 'inclusive_tax' => false, 'created_by' => auth()->id()]);
        $invoice->lines()->create(['sort' => 0, 'item_id' => $item->id, 'quantity' => 1, 'unit_id' => $item->unit1_id, 'base_quantity' => 1, 'unit_price' => 100_000, 'warehouse_id' => Warehouse::default()->id]);
        $invoice->refreshTotal();
        $this->docs->created($invoice);

        $invoice->forceFill(['nsfp' => '04002600000123'])->saveQuietly();
        $this->assertStringContainsString('04002600000123', (string) $this->docs->lockReason($invoice->fresh()));

        app(TaxFilingService::class)->clearSerial($invoice->fresh());
        $this->assertNull($invoice->fresh()->nsfp);
        $this->assertNull($this->docs->lockReason($invoice->fresh()));
        $this->assertTrue(AuditLog::query()->where('action', 'tax_serial_cleared')->where('meta->serial', '04002600000123')->exists(), 'the cleared serial is kept in the activity log');
    }
}
