<?php

namespace App\Providers;

use App\Domain\Access\Hak;
use App\Domain\Access\HakAkses;
use App\Domain\Access\MenuRegistry;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Posting\DocumentGuard;
use App\Domain\Posting\PostingService;
use App\Models\Company\AuditLog;
use App\Models\Company\Branch;
use App\Models\Company\Contact;
use App\Models\Company\Currency;
use App\Models\Company\Employee;
use App\Models\Company\Fob;
use App\Models\Company\PaymentTerm;
use App\Models\Company\Shipment;
use App\Models\Company\TaxCode;
use App\Models\GeneralLedger\Account;
use App\Models\GeneralLedger\AccountingPeriod;
use App\Models\GeneralLedger\AccountOpeningBalance;
use App\Models\GeneralLedger\DocumentRevision;
use App\Models\GeneralLedger\ExpenseAccrual;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalVoucher;
use App\Models\GeneralLedger\Posting;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemBrand;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchasing\Vendor;
use App\Models\Purchasing\VendorCategory;
use App\Models\Sales\Customer;
use App\Models\Sales\CustomerCategory;
use App\Models\Sales\DiscountCategory;
use App\Models\Sales\PriceCategory;
use App\Models\Settings\AccessGroup;
use App\Models\Settings\DocumentSeries;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Preferensi::class);
        $this->app->scoped(HakAkses::class);
        $this->app->scoped(MenuRegistry::class);
        $this->app->singleton(PostingService::class);
        $this->app->singleton(DocumentGuard::class);
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        // Stable names for polymorphic columns (audit_logs.document_type and the
        // posting tables later), independent of class names.
        Relation::enforceMorphMap([
            'user' => User::class,
            'access_group' => AccessGroup::class,
            'document_series' => DocumentSeries::class,
            'branch' => Branch::class,
            'currency' => Currency::class,
            'account' => Account::class,
            'tax_code' => TaxCode::class,
            'payment_term' => PaymentTerm::class,
            'shipment' => Shipment::class,
            'fob' => Fob::class,
            'audit_log' => AuditLog::class,
            'customer' => Customer::class,
            'customer_category' => CustomerCategory::class,
            'price_category' => PriceCategory::class,
            'discount_category' => DiscountCategory::class,
            'vendor' => Vendor::class,
            'vendor_category' => VendorCategory::class,
            'employee' => Employee::class,
            'contact' => Contact::class,
            'item' => Item::class,
            'item_category' => ItemCategory::class,
            'item_brand' => ItemBrand::class,
            'unit' => Unit::class,
            'warehouse' => Warehouse::class,
            'posting' => Posting::class,
            'journal_entry' => JournalEntry::class,
            'document_revision' => DocumentRevision::class,
            'accounting_period' => AccountingPeriod::class,
            'account_opening_balance' => AccountOpeningBalance::class,
            'journal_voucher' => JournalVoucher::class,
            'expense_accrual' => ExpenseAccrual::class,
        ]);

        // Every ability on a model resolves through the access matrix: the
        // model's screen (MenuRegistry) and the right the ability maps to.
        Gate::before(function (User $user, string $ability, array $arguments) {
            $subject = $arguments[0] ?? null;
            $class = match (true) {
                $subject instanceof Model => $subject::class,
                is_string($subject) && class_exists($subject) => $subject,
                default => null,
            };
            if ($class === null) {
                return null;
            }

            $key = app(MenuRegistry::class)->menuKeyForModel($class);
            $hak = Hak::fromAbility($ability);
            if ($key === null || $hak === null) {
                return null;
            }

            return app(HakAkses::class)->allows($user, $key, $hak);
        });
    }
}
