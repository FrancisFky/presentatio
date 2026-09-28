<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPasswordResetLink;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Services\LoginAttempts;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Session key holding the id of the admin who completed the OTP step in this session.
     */
    const OTP_SESSION_KEY = 'admin_otp_verified';

    /**
     * Wrong OTP attempts allowed before the code is invalidated and the admin is logged out.
     */
    const OTP_MAX_ATTEMPTS = 5;

    /**
     * Validity of an admin password reset link.
     */
    const PASSWORD_RESET_TTL_MINUTES = 60;

    /**
     * Show the login form for admin access
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Handle admin login attempt
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Essais comptés par compte : changer d'adresse IP ne donne pas plus d'essais
        if ($locked = LoginAttempts::lockedMessage($request->input('email'))) {
            return back()->withErrors(['email' => $locked])->withInput($request->only('email'));
        }

        if (!Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            LoginAttempts::failed($request->input('email'));

            return back()
                ->withErrors(['email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'])
                ->withInput($request->only('email'));
        }

        LoginAttempts::succeeded($request->input('email'));
        $admin = Auth::guard('admin')->user();

        // Seuls les comptes actifs se connectent (jamais activé ou désactivé : refusé)
        if ((int) $admin->status !== Admin::STATUS_ACTIVE) {
            Auth::guard('admin')->logout();

            return back()
                ->withErrors(['email' => "Ce compte n'est pas actif. Contactez un administrateur."])
                ->withInput($request->only('email'));
        }

        // Nouvelle session, qui doit passer l'étape du code avant d'accéder à l'admin
        $request->session()->regenerate();
        $request->session()->forget(self::OTP_SESSION_KEY);

        $admin->generateAndSendEmailOtp();

        return redirect()->route('otp.verify');
    }

    /**
     * Show the OTP verification form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function verifyOtp()
    {
        $admin = Auth::guard('admin')->user();

        // Already verified in this session
        if (session(self::OTP_SESSION_KEY) === $admin->id) {
            return redirect()->route('dashboard');
        }

        // Sessions opened before OTP-per-session (or with an expired code) need a fresh code
        if (!$admin->otp_code || ($admin->otp_expires_at && now()->isAfter($admin->otp_expires_at))) {
            $admin->generateAndSendEmailOtp();
        }

        $email = $admin->email;
        if(!$email){
            return redirect()->route('login')
                ->withErrors(['email' => 'Adresse e-mail requise pour la vérification.']);
        }        
        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Handle OTP verification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtpStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        }

        $admin = Auth::guard('admin')->user();
        $attemptsKey = 'admin-otp-attempts:' . $admin->id;

        // Check if OTP is valid and not expired
        if (!$admin->otp_code || !hash_equals((string) $admin->otp_code, (string) $request->otp)) {
            $attempts = Cache::increment($attemptsKey);
            Cache::put($attemptsKey, $attempts, now()->addMinutes(15));

            if ($attempts >= self::OTP_MAX_ATTEMPTS) {
                Cache::forget($attemptsKey);
                $admin->forceFill(['otp_code' => null, 'otp_expires_at' => null])->save();

                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Trop de codes incorrects. Veuillez vous reconnecter pour recevoir un nouveau code.']);
            }

            return back()
                ->withErrors(['otp' => 'Le code de vérification est incorrect.']);
        }

        if ($admin->otp_expires_at && now()->isAfter($admin->otp_expires_at)) {
            return back()
                ->withErrors(['otp' => 'Le code de vérification a expiré. Veuillez en demander un nouveau.']);
        }

        // Clear OTP after successful verification
        $admin->otp_code = null;
        $admin->otp_expires_at = null;
        $admin->save();
        Cache::forget($attemptsKey);

        // Mark only this session as verified
        $request->session()->regenerate();
        $request->session()->put(self::OTP_SESSION_KEY, $admin->id);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Bienvenue, ' . $admin->name . ' !');
    }

    /**
     * Resend OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOtp()
    {
        $admin = Auth::guard('admin')->user();
        $admin->generateAndSendEmailOtp();

        return back()
            ->with('success', 'Un nouveau code de vérification a été envoyé à votre email.');
    }

    /**
     * Show the forgot password form
     *
     * @return \Illuminate\View\View
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $admin = Admin::where('email', $request->email)->first();

        // Only active admins can reset their password; the answer is the same either way
        // so the form cannot be used to find which e-mails have an admin account.
        if ($admin && $admin->isActive()) {
            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $admin->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            Mail::to($admin->email)->send(new AdminPasswordResetLink($admin, $token, self::PASSWORD_RESET_TTL_MINUTES));
        }

        return back()
            ->with('success', 'Si un compte administrateur actif correspond à cette adresse, un lien de réinitialisation vient de lui être envoyé.');
    }

    /**
     * Show the reset password form
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function resetPassword(Request $request, $token)
    {
        $email = $request->query('email');

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Handle password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Verify the token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !$admin->isActive() || !$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()
                ->withErrors(['email' => 'Lien de réinitialisation invalide.'])
                ->withInput($request->only('email'));
        }

        if (Carbon::parse($passwordReset->created_at)->addMinutes(self::PASSWORD_RESET_TTL_MINUTES)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()
                ->withErrors(['email' => 'Ce lien de réinitialisation a expiré. Faites une nouvelle demande.'])
                ->withInput($request->only('email'));
        }

        // Update admin password
        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Logout the admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Vous êtes déconnecté.');
    }
}
