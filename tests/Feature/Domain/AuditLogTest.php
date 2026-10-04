<?php

namespace Tests\Feature\Domain;

use App\Domain\Audit\Auditor;
use App\Models\Company\AuditLog;
use App\Models\Company\Fob;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    public function test_master_changes_are_logged_with_before_and_after(): void
    {
        $admin = $this->actingAsAdmin();

        $fob = Fob::query()->create(['name' => 'Destination']);
        $fob->update(['name' => 'Destination (port)']);
        $fob->delete();

        $logs = AuditLog::query()->where('document_type', 'fob')->where('document_id', $fob->id)->orderBy('id')->get();
        $this->assertSame(['created', 'updated', 'deleted'], $logs->pluck('action')->all());
        $this->assertSame('Destination', $logs[0]->reference);
        $this->assertSame(['name' => 'Destination'], $logs[1]->meta['before']);
        $this->assertSame(['name' => 'Destination (port)'], $logs[1]->meta['after']);
        $this->assertSame($admin->id, $logs[0]->user_id);
    }

    public function test_the_log_refuses_updates_and_deletes(): void
    {
        $log = Auditor::log('created', null, 'test');

        // Each attempt in its own savepoint, so the refused statement does not abort the test's transaction.
        try {
            DB::transaction(fn () => AuditLog::query()->whereKey($log->id)->update(['action' => 'tampered']));
            $this->fail('UPDATE should have been refused');
        } catch (QueryException $e) {
            $this->assertStringContainsString('append-only', $e->getMessage());
        }

        try {
            DB::transaction(fn () => AuditLog::query()->whereKey($log->id)->delete());
            $this->fail('DELETE should have been refused');
        } catch (QueryException $e) {
            $this->assertStringContainsString('append-only', $e->getMessage());
        }

        $this->assertSame('created', $log->fresh()->action);
    }
}
