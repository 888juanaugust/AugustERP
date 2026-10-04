<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\CashAndAssetReports;
use App\Models\FixedAssets\AssetCategory;
use Filament\Forms\Components\Select;

/** Depreciation Schedule (depreciation-schedule): every asset's cost, depreciation and book value at the period's end. */
class DepreciationSchedule extends ReportPage
{
    public static function requires(): ?string
    {
        return 'fixed-assets';
    }

    public static function reportKey(): string
    {
        return 'depreciation-schedule';
    }

    public static function title(): string
    {
        return 'Depreciation Schedule';
    }

    public static function group(): string
    {
        return 'Fixed Assets';
    }

    public static function description(): string
    {
        return "Every asset with its cost, the period's depreciation, accumulated depreciation and book value at the period's end.";
    }

    protected function usesBranch(): bool
    {
        return false;
    }

    protected function defaultFilters(): array
    {
        return parent::defaultFilters() + ['category_id' => null];
    }

    protected function extraFilters(): array
    {
        return [
            Select::make('category_id')
                ->label('Asset category')
                ->options(fn () => AssetCategory::query()->orderBy('name')->pluck('name', 'id'))
                ->placeholder('All categories')
                ->nullable()
                ->native(false)
                ->live(),
        ];
    }

    protected function rows(): array
    {
        $categoryId = $this->filters['category_id'] ?? null;

        return CashAndAssetReports::depreciationSchedule($this->period(), $categoryId ? (int) $categoryId : null);
    }

    protected function columns(): array
    {
        return [
            static::text('number', 'Asset')->fontFamily('mono'),
            static::text('name', 'Name'),
            static::text('category', 'Category'),
            static::date('usage_date', 'In use from'),
            static::text('method', 'Method'),
            static::text('life', 'Life (months)')->alignEnd(),
            static::money('cost', 'Cost'),
            static::money('period', 'This period'),
            static::money('accumulated', 'Accumulated'),
            static::money('book_value', 'Book value'),
            static::text('status', 'Status'),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['Asset', 'Name', 'Category', 'In use from', 'Method', 'Life (months)', 'Cost', 'This period', 'Accumulated', 'Book value', 'Status'];
    }

    protected function exportRow(array $row): array
    {
        return [
            $row['number'],
            $row['name'],
            $row['category'],
            $row['usage_date'],
            $row['method'],
            $row['life'],
            $row['cost'],
            $row['period'],
            $row['accumulated'],
            $row['book_value'],
            $row['status'],
        ];
    }
}
