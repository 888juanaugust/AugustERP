<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralLedger\Accounts\Pages;

use App\Domain\Posting\PostingService;
use App\Filament\Resources\GeneralLedger\Accounts\AccountResource;
use App\Filament\Support\ManageMaster;
use App\Models\GeneralLedger\Account;
use App\Models\GeneralLedger\AccountOpeningBalance;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** The opening balance is not a column: it is a posted document of its own, kept in step here. */
class ManageAccounts extends ManageMaster
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('New account'))->slideOver()
                ->mutateDataUsing(fn (array $data) => self::liftOpening($data))
                ->after(fn (Model $record, array $data) => self::saveOpening($record, $data)),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table($table)->recordActions([
            EditAction::make()->slideOver()
                ->mutateRecordDataUsing(function (array $data, Account $record): array {
                    $opening = AccountOpeningBalance::query()->where('account_id', $record->id)->first();
                    $data['opening_amount'] = $opening?->amount;
                    $data['opening_date'] = $opening?->trans_date?->toDateString();

                    return $data;
                })
                ->mutateDataUsing(fn (array $data) => self::liftOpening($data))
                ->after(fn (Model $record, array $data) => self::saveOpening($record, $data)),
            DeleteAction::make()->hidden(fn (Account $r) => $r->is_system),
        ]);
    }

    private static array $opening = [];

    private static function liftOpening(array $data): array
    {
        self::$opening = ['amount' => $data['opening_amount'] ?? null, 'date' => $data['opening_date'] ?? null];
        unset($data['opening_amount'], $data['opening_date']);

        return $data;
    }

    private static function saveOpening(Model $record, array $data): void
    {
        $amount = self::$opening['amount'] ?? null;
        $date = self::$opening['date'] ?? null;
        $existing = AccountOpeningBalance::query()->where('account_id', $record->id)->first();

        if ($amount === null || $amount === '' || (int) $amount === 0) {
            if ($existing !== null) {
                app(PostingService::class)->unpost($existing);
                $existing->delete();
            }

            return;
        }

        try {
            $opening = AccountOpeningBalance::query()->updateOrCreate(
                ['account_id' => $record->id],
                ['amount' => (int) $amount, 'trans_date' => $date ?: now()->startOfYear()->toDateString()],
            );
            app(PostingService::class)->post($opening->fresh()->load('account'));
        } catch (\RuntimeException $e) {
            Notification::make()->title(__('Opening balance not posted'))->body($e->getMessage())->danger()->persistent()->send();
        }
    }
}
