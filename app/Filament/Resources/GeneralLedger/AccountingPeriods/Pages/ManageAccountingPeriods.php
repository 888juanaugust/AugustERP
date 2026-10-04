<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralLedger\AccountingPeriods\Pages;

use App\Domain\Audit\Auditor;
use App\Domain\Posting\PeriodLock;
use App\Filament\Resources\GeneralLedger\AccountingPeriods\AccountingPeriodResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountingPeriods extends ManageRecords
{
    protected static string $resource = AccountingPeriodResource::class;

    protected function getHeaderActions(): array
    {
        $lock = app(PeriodLock::class);

        return [
            Action::make('close')
                ->label('Close a month')
                ->icon('heroicon-m-lock-closed')
                ->visible(fn () => AccountingPeriodResource::canCreate())
                ->schema([
                    Select::make('month')->label('Month')
                        ->options(collect(range(1, 12))->mapWithKeys(fn (int $m) => [$m => date('F', mktime(0, 0, 0, $m, 1))])->all())
                        ->default(fn () => $lock->nextToClose()->month)->required()->native(false),
                    Select::make('year')->label('Year')
                        ->options(collect(range((int) date('Y') - 6, (int) date('Y')))->mapWithKeys(fn (int $y) => [$y => (string) $y])->all())
                        ->default(fn () => $lock->nextToClose()->year)->required()->native(false),
                    Textarea::make('notes')->label(__('fields.memo'))->rows(2),
                ])
                ->modalDescription(fn () => 'Months close in order. The next month to close is '.$lock->nextToClose()->format('F Y').'. Nothing dated in a closed month can be added, changed or deleted.')
                ->action(function (array $data) use ($lock): void {
                    try {
                        $period = $lock->close((int) $data['year'], (int) $data['month'], auth()->id(), $data['notes'] ?? null);
                        Auditor::log('period_closed', $period, $period->label());
                        Notification::make()->title($period->label().' closed')->success()->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()->title('Cannot close')->body($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
