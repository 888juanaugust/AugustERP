<?php

declare(strict_types=1);

namespace App\Domain\Posting\Contracts;

/** Something downstream of a document that forbids changing or deleting it. */
interface Blocker
{
    /** The reason the document is locked, or null when this blocker does not apply. */
    public function blocks(Postable $document): ?string;
}
