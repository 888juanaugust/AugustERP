<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Documents\LineCalculator;
use App\Domain\Inventory\Units\UnitConverter;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Format;
use App\Models\Company\Employee;
use App\Models\Company\TaxCode;
use App\Models\GeneralLedger\Account;
use App\Models\Inventory\Item;
use App\Models\Inventory\Warehouse;
use App\Models\Sales\Customer;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Alignment;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;

/**
 * The standard's priced-document form, in DESIGN.md's skin: a header
 * (party, date, number), the line grid (item, quantity, unit, price, discount,
 * tax, warehouse), "Other info", "Other charges" and live totals computed by
 * the same LineCalculator the posting uses.
 */
final class PricedDocumentForm
{
    public static function header(Select $party, TransactionType $type, string $numberLabel = 'Number', array $extra = []): Section
    {
        return Section::make()
            ->columns(3)
            ->schema([
                $party->columnSpan(1),
                DatePicker::make('trans_date')->label(__('fields.trans_date'))->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today())->live(onBlur: true),
                NumberFields::make($type, $numberLabel),
                ...$extra,
            ]);
    }

    public static function money(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->default(0);
    }

    /**
     * @param  list<Component>  $before  components shown above the grid (a Pull action, say)
     * @param  bool  $groupItems  offer group items (selling documents only; a group is never bought)
     */
    public static function linesTab(array $before = [], bool $prices = true, bool $warehouse = true, bool $processed = false, ?\Closure $priceResolver = null, bool $salesman = false, ?bool $pricesEditable = null, bool $groupItems = false): Tab
    {
        $seesCost = app(HakAkses::class)->allowsSpecial(auth()->user(), HakKhusus::SeeCost);
        $columns = [TableColumn::make(__('Item'))];
        $columns[] = TableColumn::make(__('Quantity'))->alignment(Alignment::End);
        $columns[] = TableColumn::make(__('Unit'));
        if ($prices) {
            $columns[] = TableColumn::make(__('Unit price'))->alignment(Alignment::End);
            $columns[] = TableColumn::make(__('Disc %'))->alignment(Alignment::End);
            $columns[] = TableColumn::make(__('Amount'))->alignment(Alignment::End);
            $columns[] = TableColumn::make(__('Tax'));
        }
        if ($warehouse) {
            $columns[] = TableColumn::make(__('Warehouse'));
        }
        if ($salesman) {
            $columns[] = TableColumn::make(__('Salesperson'));
        }
        if ($processed) {
            $columns[] = TableColumn::make(__('Processed'))->alignment(Alignment::End);
        }
        $columns[] = TableColumn::make(__('Memo'));

        $fields = [
            LineItemFields::item(groups: $groupItems)->afterStateUpdated(function (Set $set, Get $get, $state) use ($priceResolver): void {
                $item = $state ? Item::query()->find($state) : null;
                $set('unit_id', $item?->unit1_id);
                if ($item && ! $get('source_line_id')) {
                    $set('unit_price', $priceResolver ? $priceResolver($item, $get) : (string) $item->purchase_price);
                    $set('tax_code_id', $item->tax1_id ?? TaxCode::default()?->id);
                }
                LineItemFields::syncBase($set, $get);
            }),
            LineItemFields::quantity()->minValue(0.0001),
            LineItemFields::unit(),
        ];
        $pricesEditable ??= ! $salesman || app(HakAkses::class)->allowsSpecial(auth()->user(), HakKhusus::ChangeSellingPrice);
        if ($prices) {
            $fields[] = TextInput::make('unit_price')->numeric()->default(0)->live(onBlur: true)->prefix(Format::symbol())->readOnly(! $pricesEditable);
            $fields[] = TextInput::make('discount_percent')->numeric()->default(0)->minValue(0)->maxValue(100)->live(onBlur: true);
            $fields[] = Placeholder::make('amount_preview')->hiddenLabel()->content(fn (Get $get) => Format::number(self::lineAmount($get)));
            $fields[] = Select::make('tax_code_id')->options(fn () => TaxCode::query()->where('is_active', true)->orderBy('description')->pluck('description', 'id'))->native(false)->live();
        }
        if (! $prices) {
            // Price-less grids (receipts) still carry the price the goods came in at, for the ledger.
            $fields[] = Hidden::make('unit_price')->default(0)->dehydrated();
            $fields[] = Hidden::make('discount_percent')->default(0)->dehydrated();
            $fields[] = Hidden::make('tax_code_id')->dehydrated();
        }
        if ($warehouse) {
            $fields[] = Select::make('warehouse_id')->options(fn () => Warehouse::query()->visibleTo(auth()->user())->where('is_system', false)->where('is_active', true)->orderBy('name')->pluck('name', 'id'))->native(false)->required()
                ->default(fn () => Warehouse::default()?->id);
        }
        if ($salesman) {
            $fields[] = Select::make('salesman_id')->options(fn () => Employee::query()->salesmen()->orderBy('name')->pluck('name', 'id'))->native(false)
                ->default(fn (Get $get) => $get('../../customer_id') ? Customer::query()->find($get('../../customer_id'))?->salesman_id : null);
        }
        if ($processed) {
            $fields[] = TextInput::make('processed_quantity')->numeric()->disabled()->dehydrated(false)->default(0);
        }
        $fields[] = TextInput::make('memo')->maxLength(255);
        $fields[] = LineItemFields::baseQuantity();
        $fields[] = Hidden::make('source_line_type')->dehydrated();
        $fields[] = Hidden::make('source_line_id')->dehydrated();
        $fields[] = Hidden::make('discount_amount')->default(0)->dehydrated();

        return Tab::make(__('fields.lines'))->schema([
            ...$before,
            Repeater::make('lines')
                ->hiddenLabel()
                ->relationship()
                ->orderColumn('sort')
                ->table($columns)
                ->schema($fields)
                ->minItems(1)
                ->defaultItems(1)
                ->live()
                ->addActionLabel('Add line')
                ->mutateRelationshipDataBeforeCreateUsing(fn (array $data) => self::normaliseLine($data))
                ->mutateRelationshipDataBeforeSaveUsing(fn (array $data) => self::normaliseLine($data)),
            ...($prices ? [self::totals()] : []),
        ]);
    }

    /** Every money column present and numeric, the base quantity in step, before a line is saved. */
    public static function normaliseLine(array $data): array
    {
        $data = LineItemFields::fillBaseQuantities([$data])[0];
        foreach (['unit_price', 'discount_percent', 'discount_amount', 'amount', 'dpp_amount', 'tax_amount'] as $column) {
            if (! isset($data[$column]) || $data[$column] === '') {
                $data[$column] = 0;
            }
        }
        foreach (['source_line_type', 'source_line_id', 'tax_code_id', 'warehouse_id', 'unit_id'] as $column) {
            if (isset($data[$column]) && $data[$column] === '') {
                $data[$column] = null;
            }
        }

        return $data;
    }

    private static function lineAmount(Get $get): int
    {
        $result = LineCalculator::compute([[
            'quantity' => $get('quantity') ?: 0,
            'unit_price' => $get('unit_price') ?: 0,
            'discount_percent' => $get('discount_percent') ?: 0,
            'discount_amount' => 0,
            'tax_code_id' => null,
        ]], false, false);

        return $result['lines'][0]['amount'] ?? 0;
    }

    public static function totals(): Placeholder
    {
        return Placeholder::make('totals')
            ->hiddenLabel()
            ->content(function (Get $get): HtmlString {
                $result = LineCalculator::compute(
                    array_values((array) $get('lines')),
                    (bool) $get('taxable'),
                    (bool) $get('inclusive_tax'),
                    (string) ($get('discount_percent') ?: 0),
                    0,
                    array_values((array) $get('charges')),
                );
                $rows = [
                    [__('fields.subtotal'), $result['subtotal']],
                    [__('fields.discount'), -$result['discount_amount']],
                    [__('fields.charges_total'), $result['charges_total']],
                    [__('fields.dpp_total'), $result['dpp_total']],
                    [__('fields.tax_total'), $result['tax_total']],
                ];
                $html = '<div class="ae-totals">';
                foreach ($rows as [$label, $value]) {
                    if ($value === 0 && ! in_array($label, [__('fields.subtotal'), __('fields.tax_total')], true)) {
                        continue;
                    }
                    $html .= '<div class="ae-totals-row"><span>'.e($label).'</span><span class="ae-money">'.e(Format::number($value)).'</span></div>';
                }
                $html .= '<div class="ae-totals-row ae-totals-grand"><span>'.e(__('fields.total')).'</span><span class="ae-money">'.e(Format::rupiah($result['total'])).'</span></div></div>';

                return new HtmlString($html);
            });
    }

    /** @param  list<Component>  $extra */
    public static function otherInfoTab(array $extra = [], bool $shipping = true): Tab
    {
        return Tab::make(__('fields.other_info'))->schema([
            ...$extra,
            BranchFields::select(),
            Textarea::make('to_address')->label(__('Address'))->rows(2),
            Textarea::make('description')->label(__('fields.description'))->rows(2),
            Toggle::make('taxable')->label(__('fields.taxable'))->default(true)->live(),
            Toggle::make('inclusive_tax')->label(__('fields.inclusive_tax'))->default(false)->live(),
            TextInput::make('discount_percent')->label(__('Discount on the total (%)'))->numeric()->minValue(0)->maxValue(100)->default(0)->live(onBlur: true),
            ...($shipping ? [
                DatePicker::make('ship_date')->label(__('fields.ship_date'))->native(false)->displayFormat(Format::DATE_INPUT),
                Select::make('shipment_id')->label(__('fields.shipment'))->relationship('shipment', 'name')->preload()->native(false),
                Select::make('fob_id')->label(__('fields.fob'))->relationship('fob', 'name')->preload()->native(false),
            ] : []),
        ])->columns(2);
    }

    public static function chargesTab(bool $allocateToCost = false): Tab
    {
        $columns = [TableColumn::make(__('Charge')), TableColumn::make(__('Amount'))->alignment(Alignment::End), TableColumn::make(__('Description'))];
        $fields = [
            Select::make('account_id')->options(fn () => Account::options(AccountType::Expense, AccountType::OtherExpense, AccountType::CostOfSales, AccountType::OtherCurrentAsset, AccountType::OtherIncome))->searchable()->required()->native(false),
            self::money('amount', 'Amount')->live(onBlur: true),
            TextInput::make('description')->maxLength(255),
        ];
        if ($allocateToCost) {
            $columns[] = TableColumn::make(__('Into item cost'));
            $fields[] = Toggle::make('allocate_to_cost')->default(false);
        }

        return Tab::make(__('fields.other_charges'))->schema([
            Repeater::make('charges')
                ->hiddenLabel()
                ->relationship()
                ->orderColumn('sort')
                ->table($columns)
                ->schema($fields)
                ->defaultItems(0)
                ->live()
                ->addActionLabel('Add charge'),
        ]);
    }

    /** The remaining lines of an upstream document, shaped for the grid, pointing back at their source. */
    public static function pulledLines(iterable $lines, string $sourceLineType, bool $withPrices = true): array
    {
        $out = [];
        foreach ($lines as $line) {
            $remaining = $line->remainingQuantity();
            if (! BigDecimal::of($remaining)->isPositive()) {
                continue;
            }
            $ratio = UnitConverter::ratio($line->item->load('units'), $line->unit_id ?? $line->item->unit1_id);
            $row = [
                'item_id' => $line->item_id,
                'quantity' => (string) BigDecimal::of($remaining)->dividedBy($ratio, 4, RoundingMode::HalfUp),
                'unit_id' => $line->unit_id ?? $line->item->unit1_id,
                'base_quantity' => $remaining,
                'warehouse_id' => $line->warehouse_id ?? null,
                'memo' => $line->memo,
                'source_line_type' => $sourceLineType,
                'source_line_id' => $line->id,
            ];
            if (isset($line->salesman_id)) {
                $row['salesman_id'] = $line->salesman_id;
            }
            if ($withPrices) {
                $row['unit_price'] = (string) ($line->unit_price ?? 0);
                $row['discount_percent'] = (string) ($line->discount_percent ?? 0);
                $row['tax_code_id'] = $line->tax_code_id ?? null;
            }
            $out[] = $row;
        }

        return $out;
    }
}
