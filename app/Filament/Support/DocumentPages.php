<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Posting\DocumentRepository;
use Carbon\CarbonImmutable;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * What every document page shares: the posting layer runs around Filament's
 * own saving (header, then line repeaters, then these hooks), and a locked
 * document explains itself instead of failing.
 */
final class DocumentPages
{
    public static function afterCreated(Model $record): void
    {
        app(DocumentRepository::class)->created($record);
    }

    /** @return array the snapshot to hand to afterUpdated() */
    public static function beforeUpdate(Model $record, array $data): array
    {
        $newDate = isset($data['trans_date']) ? CarbonImmutable::parse($data['trans_date']) : null;

        try {
            return app(DocumentRepository::class)->beforeUpdate($record, $newDate);
        } catch (RuntimeException $e) {
            Notification::make()->title('Cannot save')->body($e->getMessage())->danger()->persistent()->send();
            throw new Halt;
        }
    }

    public static function afterUpdated(Model $record, array $before): void
    {
        app(DocumentRepository::class)->updated($record, $before);
    }

    /**
     * Repeater items are keyed; a relationship repeater also reloads from the
     * record on fill, so prefilled lines go straight into the page state.
     */
    public static function keyedRows(array $rows): array
    {
        $keyed = [];
        foreach ($rows as $row) {
            $keyed[(string) Str::uuid()] = $row;
        }

        return $keyed;
    }

    public static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->using(function (Model $record): bool {
                try {
                    app(DocumentRepository::class)->delete($record);

                    return true;
                } catch (RuntimeException $e) {
                    Notification::make()->title('Cannot delete')->body($e->getMessage())->danger()->persistent()->send();

                    return false;
                }
            });
    }
}
