<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOtpVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // The OTP step is tracked per session: verifying the code on one device
        // must not unlock another session opened with the same password.
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();

            if ($request->session()->get(\App\Http\Controllers\AuthController::OTP_SESSION_KEY) !== $admin->id) {
                return redirect()->route('otp.verify', ['email' => $admin->email])
                    ->with('warning', 'Veuillez compléter la vérification OTP pour continuer.');
            }
        }

        return $next($request);
    }
} 