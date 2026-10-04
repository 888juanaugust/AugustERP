<?php

declare(strict_types=1);

namespace App\Filament\Resources\FixedAssets\FixedAssets\Pages;

use App\Domain\FixedAssets\DepreciationRun;
use App\Domain\Shared\Format;
use App\Filament\Resources\FixedAssets\FixedAssets\FixedAssetResource;
use App\Filament\Support\ListDocuments;
use App\Models\FixedAssets\FixedAsset;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

/** The asset register, with the monthly depreciation run in its header. */
class ListFixedAssets extends ListDocuments
{
    protected static string $resource = FixedAssetResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'active' => Tab::make('In use')->modifyQueryUsing(fn (Builder $query) => $query->where('status', FixedAsset::ACTIVE)),
            'disposed' => Tab::make('Disposed')->modifyQueryUsing(fn (Builder $query) => $query->where('status', FixedAsset::DISPOSED)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            Action::make('runDepreciation')
                ->label('Run depreciation')
                ->icon('heroicon-m-calculator')
                ->color('gray')
                ->schema([
                    DatePicker::make('until')->label('Up to the month of')->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                ])
                ->requiresConfirmation()
                ->modalDescription('Posts one month of depreciation for every asset in use, for each month not yet posted up to that month. Months already posted are left alone.')
                ->action(function (array $data): void {
                    $result = app(DepreciationRun::class)->upTo($data['until']);

                    Notification::make()
                        ->title("{$result['posted']} month(s) posted")
                        ->body(Format::money($result['amount']).' of depreciation')
                        ->success()
                        ->send();

                    if ($result['skipped'] !== []) {
                        Notification::make()
                            ->title(count($result['skipped']).' asset(s) skipped')
                            ->body(implode("\n", $result['skipped']))
                            ->warning()
                            ->persistent()
                            ->send();
                    }
                })
                ->visible(fn (): bool => static::getResource()::canCreate()),
        ];
    }
}
