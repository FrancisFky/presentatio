<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Site public : la langue vient de l'URL (/fr/…, /en/…). Elle est ajoutée
 * d'office aux liens générés par route(), sans la repasser à chaque appel.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        abort_unless(Locales::isSupported($locale), 404);

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Le paramètre ne doit pas arriver dans les contrôleurs
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
