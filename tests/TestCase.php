<?php

namespace Tests;

use App\Models\User;
use App\Modules\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /** Switch every module on, so a test sees every screen whatever the defaults say. */
    protected function enableAllModules(): void
    {
        app(ModuleRegistry::class)->enableAll();
    }

    /** Drop the per-request singletons, as a new HTTP request would (Filament mounts the sidebar once per request). */
    protected function freshRequest(): void
    {
        app()->forgetScopedInstances();
    }

    /** Log in as an administrator, who passes every access check. */
    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create([
            'access_type' => 'administrator',
            'is_active' => true,
        ]);
        $this->actingAs($user);

        return $user;
    }
}
