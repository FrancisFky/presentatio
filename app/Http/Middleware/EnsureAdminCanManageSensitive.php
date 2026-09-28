<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Suppressions et réglages du site : réservés aux administrateurs,
 * pas aux éditeurs.
 */
class EnsureAdminCanManageSensitive
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            Auth::guard('admin')->user()?->canManageAdmins(),
            403,
            'Cette action est réservée aux administrateurs.'
        );

        return $next($request);
    }
}
