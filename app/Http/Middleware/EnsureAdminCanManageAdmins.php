<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminCanManageAdmins
{
    /**
     * Only super admins and admins can access admin management.
     * When the route targets a specific admin, the current admin must be allowed to manage that account.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $current = Auth::guard('admin')->user();

        if (!$current || !$current->canManageAdmins()) {
            abort(403, 'Vous n\'avez pas accès à la gestion des administrateurs.');
        }

        $target = $request->route('admin');

        if ($target instanceof \App\Models\Admin && !$current->canManage($target)) {
            abort(403, 'Vous ne pouvez pas gérer cet administrateur.');
        }

        return $next($request);
    }
}
