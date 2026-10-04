<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * The first administrator. The password comes from ADMIN_PASSWORD in the
 * environment; when it is unset or blank, a local-only default so the panel
 * opens on a dev machine. Production sets the variable.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL') ?: 'admin@august.test'],
            [
                'name' => env('ADMIN_NAME') ?: 'Administrator',
                'password' => Hash::make(env('ADMIN_PASSWORD') ?: 'password'),
                'access_type' => 'administrator',
                'is_active' => true,
            ],
        );
    }
}
