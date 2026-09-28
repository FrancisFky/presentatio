<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }

        $admin = Auth::guard('admin')->user();

        // Check if admin is deactivated
        if ($admin->status == Admin::STATUS_DEACTIVATED) {
            // Allow logout route
            if ($request->route()->getName() === 'admin.logout') {
                return $next($request);
            }
            
            // Redirect to contact page for all other routes
            return redirect()->route('admin.contact');
        }

        return $next($request);
    }
} 