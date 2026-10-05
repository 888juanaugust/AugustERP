<?php

declare(strict_types=1);

namespace App\Domain\Printing;

use App\Domain\Access\Hak;
use App\Domain\Access\HakAkses;
use App\Domain\Access\MenuRegistry;
use App\Domain\Approval\ApprovalEngine;
use App\Domain\Audit\Auditor;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Models\Company\PrintLayout;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * One print of one document: the layout chosen for its type (the user's
 * own, else the default), the company header from the preferences, the
 * right checked, the document marked printed and the print logged.
 */
final class PrintJob
{
    public function __construct(private readonly HakAkses $akses, private readonly MenuRegistry $menus, private readonly Preferensi $prefs) {}

    /** @return array{document: Model, meta: array, layout: array, company: array, title: string} */
    public function prepare(string $alias, int $id, User $user, ?int $layoutId = null): array
    {
        $meta = Printable::for($alias) ?? throw new RuntimeException("Nothing called {$alias} prints.");
        $document = $meta['model']::query()->findOrFail($id);
        $menu = $this->menus->menuKeyForModel($document::class);
        if ($menu !== null && ! $this->akses->allows($user, $menu, Hak::Print)) {
            throw new RuntimeException('Printing this document takes the print right on its screen.');
        }
        app(ApprovalEngine::class)->assertApproved($document, __('is not approved; it cannot be printed yet.'));

        $layout = $this->layoutFor($meta['type']->value, $user, $layoutId);

        if (! $document->getAttribute('is_printed') && $document->getConnection()->getSchemaBuilder()->hasColumn($document->getTable(), 'is_printed')) {
            $document->forceFill(['is_printed' => true])->saveQuietly();
        }
        Auditor::log('printed', $document, (string) $document->getAttribute('number'), ['layout' => $layout['name'] ?? 'Standard']);

        return [
            'document' => $document,
            'meta' => $meta,
            'layout' => $layout,
            'company' => [
                'name' => (string) ($this->prefs->get(PreferensiKey::CompanyName) ?: $this->prefs->get(PreferensiKey::TaxCompanyName) ?: config('app.name')),
                'address' => (string) $this->prefs->get(PreferensiKey::CompanyAddress),
                'phone' => (string) $this->prefs->get(PreferensiKey::CompanyPhone),
                'email' => (string) $this->prefs->get(PreferensiKey::CompanyEmail),
                'npwp' => (string) $this->prefs->get(PreferensiKey::CompanyNpwp),
            ],
            'title' => (string) (($layout['title'] ?? null) ?: $meta['title']),
        ];
    }

    /** @return array<string, mixed> the layout's settings plus its name */
    public function layoutFor(string $transactionType, User $user, ?int $layoutId = null): array
    {
        $query = PrintLayout::query()->where('transaction_type', $transactionType)
            ->where(fn ($q) => $q->where('used_all_user', true)->orWhereHas('users', fn ($u) => $u->whereKey($user->id)));
        $layout = $layoutId ? (clone $query)->find($layoutId) : null;
        $layout ??= (clone $query)->where('is_default', true)->first() ?? $query->orderBy('id')->first();

        return ($layout?->settings() ?? PrintLayout::DEFAULTS) + ['name' => $layout?->name ?? 'Standard', 'id' => $layout?->id];
    }
}
