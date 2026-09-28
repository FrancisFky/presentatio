<?php

namespace App\Http\Middleware;

use App\Models\AdminAuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journal des actions des administrateurs : toute modification, Jamais le contenu des formulaires (mots de passe).
 */
class AuditAdminActions
{
    /** Consultations qui méritent aussi une trace */
    const AUDITED_READS = [];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $response;
        }

        // « Dernière activité » à jour, sans écrire à chaque clic
        if (Cache::add('admin-activity:' . $admin->id, true, now()->addMinutes(5))) {
            $admin->forceFill(['last_activity_at' => now()])->saveQuietly();
        }

        $route = $request->route();
        $name = $route?->getName();

        if ($request->isMethodSafe() && !in_array($name, self::AUDITED_READS, true)) {
            return $response;
        }

        AdminAuditLog::create([
            'admin_id' => $admin->id,
            'action' => $name ?? $request->path(),
            'method' => $request->method(),
            'targets' => collect($route?->parameters() ?? [])
                ->map(fn ($value) => $value instanceof Model
                    ? class_basename($value) . ' #' . $value->getKey() . (isset($value->name) ? ' (' . $value->name . ')' : '')
                    : (string) $value)
                ->all() ?: null,
            'status' => $response->getStatusCode(),
            'ip' => $request->ip(),
        ]);

        return $response;
    }
}
