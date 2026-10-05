<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Shared\Format;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/** The date-range filter every document list carries. */
final class DocumentListFilters
{
    public static function dateRange(string $column = 'trans_date', string $label = 'Date'): Filter
    {
        return Filter::make($column)
            ->schema([
                DatePicker::make('from')->label(__(':label from', ['label' => $label]))->native(false),
                DatePicker::make('until')->label(__('until'))->native(false),
            ])
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate($column, '>=', $d))
                ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate($column, '<=', $d)))
            ->indicateUsing(fn (array $data) => array_filter([
                ($data['from'] ?? null) ? "{$label} from ".Format::date($data['from']) : null,
                ($data['until'] ?? null) ? "{$label} until ".Format::date($data['until']) : null,
            ]));
    }
}
