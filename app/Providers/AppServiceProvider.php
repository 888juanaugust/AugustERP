<?php

namespace App\Providers;

use App\Domain\Access\Hak;
use App\Domain\Access\HakAkses;
use App\Domain\Access\MenuRegistry;
use App\Domain\Fulfilment\FulfilmentService;
use App\Domain\Inventory\Costing\Recoster;
use App\Domain\Inventory\StockLedger;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Posting\Blockers\ReferencedBlocker;
use App\Domain\Posting\Blockers\SettledBlocker;
use App\Domain\Posting\DocumentGuard;
use App\Domain\Posting\PostingService;
use App\Domain\Settlement\AllocationLedger;
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
use App\Models\Inventory\InventoryAdjustment;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemBrand;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\ItemTransfer;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\StockOpnameOrder;
use App\Models\Inventory\StockOpnameResult;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchasing\GoodsReceipt;
use App\Models\Purchasing\GoodsReceiptLine;
use App\Models\Purchasing\PaymentOrder;
use App\Models\Purchasing\PurchaseDownPayment;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceLine;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderLine;
use App\Models\Purchasing\PurchasePayment;
use App\Models\Purchasing\PurchaseRequisition;
use App\Models\Purchasing\PurchaseRequisitionLine;
use App\Models\Purchasing\PurchaseReturn;
use App\Models\Purchasing\PurchaseReturnLine;
use App\Models\Purchasing\Vendor;
use App\Models\Purchasing\VendorCategory;
use App\Models\Purchasing\VendorClaim;
use App\Models\Purchasing\VendorPrice;
use App\Models\Sales\Customer;
use App\Models\Sales\CustomerCategory;
use App\Models\Sales\DiscountCategory;
use App\Models\Sales\PriceCategory;
use App\Models\Settings\AccessGroup;
use App\Models\Settings\DocumentSeries;
use App\Models\Settlement\PaymentAllocation;
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
        $this->app->singleton(Recoster::class);
        $this->app->singleton(FulfilmentService::class);
        $this->app->singleton(Recoster::class);
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
            'stock_movement' => StockMovement::class,
            'inventory_adjustment' => InventoryAdjustment::class,
            'item_transfer' => ItemTransfer::class,
            'stock_opname_order' => StockOpnameOrder::class,
            'stock_opname_result' => StockOpnameResult::class,
            'payment_allocation' => PaymentAllocation::class,
            'purchase_requisition' => PurchaseRequisition::class,
            'purchase_requisition_line' => PurchaseRequisitionLine::class,
            'vendor_price' => VendorPrice::class,
            'purchase_order' => PurchaseOrder::class,
            'purchase_order_line' => PurchaseOrderLine::class,
            'goods_receipt' => GoodsReceipt::class,
            'goods_receipt_line' => GoodsReceiptLine::class,
            'purchase_down_payment' => PurchaseDownPayment::class,
            'purchase_invoice' => PurchaseInvoice::class,
            'purchase_invoice_line' => PurchaseInvoiceLine::class,
            'purchase_payment' => PurchasePayment::class,
            'purchase_return' => PurchaseReturn::class,
            'purchase_return_line' => PurchaseReturnLine::class,
            'vendor_claim' => VendorClaim::class,
            'payment_order' => PaymentOrder::class,
        ]);

        // The stock ledger writes the movements every posting declares.
        $postings = $this->app->make(PostingService::class);
        $postings->extend(fn ($posting, $builder) => $this->app->make(StockLedger::class)->write($posting, $builder));
        $postings->onUnpost(fn ($posting) => $this->app->make(StockLedger::class)->unwrite($posting));

        // The settlement ledger writes what each payment applied to which invoice.
        $postings->extend(fn ($posting, $builder) => $this->app->make(AllocationLedger::class)->write($posting, $builder));
        $postings->onUnpost(fn ($posting) => $this->app->make(AllocationLedger::class)->unwrite($posting));

        // What keeps a document from changing: payments applied to it, documents made from it.
        $guard = $this->app->make(DocumentGuard::class);
        $guard->addBlocker($this->app->make(SettledBlocker::class));
        $guard->addBlocker($this->app->make(ReferencedBlocker::class));

        // Who pulls lines from whom, so processed quantities and statuses follow.
        $fulfilment = $this->app->make(FulfilmentService::class);
        $fulfilment->register(PurchaseRequisitionLine::class, PurchaseOrderLine::class);
        $fulfilment->register(PurchaseOrderLine::class, GoodsReceiptLine::class);
        $fulfilment->register(PurchaseOrderLine::class, PurchaseInvoiceLine::class);
        $fulfilment->register(GoodsReceiptLine::class, PurchaseInvoiceLine::class);

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
