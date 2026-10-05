<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** A user who must change their password sees only the profile page until they have. */
class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user instanceof User || ! $user->password_change_required) {
            return $next($request);
        }
        // The profile page itself, its Livewire requests and signing out stay open.
        if ($request->routeIs('filament.admin.auth.profile', 'filament.admin.auth.logout') || $request->hasHeader('X-Livewire')) {
            return $next($request);
        }

        return redirect()->to(EditProfile::getUrl());
    }
}
