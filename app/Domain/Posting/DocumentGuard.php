<?php

declare(strict_types=1);

namespace App\Domain\Posting;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Posting\Contracts\Blocker;
use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\Exceptions\DocumentLockedException;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Whether a posted document may change or go: the period of its current date
 * and of its new date must be open, nothing downstream may depend on it, and
 * editing another user's document takes a special right.
 */
final class DocumentGuard
{
    /** @var list<Blocker> */
    private array $blockers = [];

    public function __construct(private readonly PeriodLock $periods, private readonly HakAkses $akses) {}

    public function addBlocker(Blocker $blocker): void
    {
        $this->blockers[] = $blocker;
    }

    public function assertMutable(Postable&Model $document, ?CarbonInterface $newDate = null): void
    {
        $this->periods->assertOpen($document->postingDate(), $document->postingNumber());
        if ($newDate !== null) {
            $this->periods->assertOpen($newDate, $document->postingNumber());
        }

        $creator = $document->getAttribute('created_by');
        $user = auth()->user();
        if ($user !== null && $creator !== null && $creator !== $user->id && ! $this->akses->allowsSpecial($user, HakKhusus::EditOthersTransactions)) {
            throw new DocumentLockedException("{$document->postingNumber()} was entered by another user; changing it takes the \"edit other users' transactions\" right.");
        }

        foreach ($this->blockers as $blocker) {
            $reason = $blocker->blocks($document);
            if ($reason !== null) {
                throw new DocumentLockedException("{$document->postingNumber()} cannot be changed: {$reason}");
            }
        }
    }

    /** The reason the document is locked, for a disabled button, or null. */
    public function lockReason(Postable&Model $document): ?string
    {
        try {
            $this->assertMutable($document);
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }

        return null;
    }
}
