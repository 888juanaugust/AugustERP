<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Shared\Format;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/**
 * The reference system's "Ambil": pick open upstream documents of the chosen
 * party and append their remaining lines to the grid, each line pointing at
 * its source so fulfilment follows.
 */
final class PullAction
{
    /**
     * @param  Closure(Get): iterable  $documents  the open upstream documents for the current form state (number + id)
     * @param  Closure(int): array  $lines  the pulled rows of one upstream document (PricedDocumentForm::pulledLines)
     */
    public static function make(string $label, string $partyField, Closure $documents, Closure $lines): Action
    {
        return Action::make('pull')
            ->label($label)
            ->icon('heroicon-m-arrow-down-tray')
            ->color('gray')
            ->visible(fn (Get $get) => (bool) $get($partyField))
            ->schema(fn (Get $get) => [
                CheckboxList::make('sources')
                    ->label('Open documents')
                    ->options(collect($documents($get))->mapWithKeys(fn ($doc) => [$doc->id => $doc->number.' · '.Format::date($doc->trans_date).($doc->description ? " · {$doc->description}" : '')])->all())
                    ->required()
                    ->bulkToggleable(),
            ])
            ->action(function (Set $set, Get $get, array $data) use ($lines): void {
                $existing = array_filter((array) $get('lines'), fn ($l) => ! empty($l['item_id']));
                $added = 0;
                foreach ($data['sources'] ?? [] as $id) {
                    foreach ($lines((int) $id) as $row) {
                        $existing[(string) Str::uuid()] = $row;
                        $added++;
                    }
                }
                $set('lines', $existing);
                Notification::make()->title($added ? "{$added} line(s) pulled" : 'Nothing left to pull')->success()->send();
            });
    }
}
