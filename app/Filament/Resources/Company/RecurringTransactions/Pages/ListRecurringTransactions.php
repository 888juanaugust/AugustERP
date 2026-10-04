<?php

declare(strict_types=1);

namespace App\Filament\Resources\Company\RecurringTransactions\Pages;

use App\Domain\Company\RecurringRunner;
use App\Filament\Resources\Company\RecurringTransactions\RecurringTransactionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use RuntimeException;

class ListRecurringTransactions extends ListRecords
{
    protected static string $resource = RecurringTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runDue')
                ->label('Run everything due')
                ->icon('heroicon-m-play')
                ->color('gray')
                ->visible(fn (): bool => RecurringTransactionResource::canCreate())
                ->requiresConfirmation()
                ->modalDescription('Makes and posts a document for every active schedule whose next run is today or earlier.')
                ->action(function (): void {
                    try {
                        $made = app(RecurringRunner::class)->runDue();
                    } catch (RuntimeException $e) {
                        Notification::make()->title('Cannot run')->body($e->getMessage())->danger()->persistent()->send();

                        return;
                    }
                    if ($made === []) {
                        Notification::make()->title('Nothing was due')->info()->send();

                        return;
                    }
                    Notification::make()->title(count($made).' document(s) made')->body(implode("\n", $made))->success()->send();
                }),
            CreateAction::make()->label('New recurring transaction'),
        ];
    }
}
