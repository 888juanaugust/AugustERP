<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

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
