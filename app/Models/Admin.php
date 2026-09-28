<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpSent;
use App\Mail\ActivationLinkSent;
use Illuminate\Support\Str;

class Admin extends Authenticatable
{
    use SoftDeletes, Notifiable;
    
    protected $guarded = [];

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVATED = 2;

    // Rôles : un éditeur (member) gère le contenu ; supprimer, régler le site
    // et gérer les comptes est réservé aux administrateurs.
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';

    const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super administrateur',
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_MEMBER => 'Éditeur',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password' => 'hashed',
        'last_activity_at' => 'datetime',
        'activation_sent_at' => 'datetime',
    ];

    protected $appends = [
        'status_name',
        'status_badge',
        'role_name',
    ];

    public function getRouteKeyName()
    {
        return "slug";
    }

    /** Code fixe du développement (ADMIN_TEST_OTP), jamais en dehors de « local » */
    public static function testOtp(): ?string
    {
        $code = config('auth.test_otp');

        return app()->environment('local') && preg_match('/^\d{6}$/', (string) $code) ? (string) $code : null;
    }

    public function generateAndSendEmailOtp()
    {
        $this->update([
            'otp_code' => self::testOtp() ?? (string) random_int(100000, 999999),
            'otp_expires_at' => now()->addMinutes(10),
        ]);
        Mail::to($this->email)->send(new OtpSent($this));
        return true;
    }

    public function getStatusNameAttribute()
    {
        if ($this->status == self::STATUS_ACTIVE) {
            return "active";
        }
        if ($this->status == self::STATUS_DEACTIVATED) {
            return "deactivated";
        }
        return "inactive";
    }

    public function getRoleNameAttribute()
    {
        return self::ROLES[$this->role] ?? 'Inconnu';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="ph ph-check-circle mr-1"></i> Actif</span>',
            self::STATUS_DEACTIVATED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="ph ph-x-circle mr-1"></i> Désactivé</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><i class="ph ph-pause-circle mr-1"></i> Inactif</span>',
        };
    }

    public function generateActivationToken()
    {
        $token = Str::random(64);
        $this->update([
            'activation_token' => $token,
            'activation_sent_at' => now(),
        ]);
        return $token;
    }

    public function sendActivationEmail()
    {
        $token = $this->generateActivationToken();
        Mail::to($this->email)->send(new ActivationLinkSent($this, $token));
        return true;
    }

    public function activate()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'activation_token' => null,
            'activation_sent_at' => null,
        ]);
    }

    public function deactivate()
    {
        $this->update([
            'status' => self::STATUS_DEACTIVATED,
        ]);
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isDeactivated()
    {
        return $this->status == self::STATUS_DEACTIVATED;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Super admins and admins can access admin management, members cannot.
     */
    public function canManageAdmins(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true);
    }

    /**
     * Roles this admin is allowed to assign and manage:
     * super admins manage admins and members, admins manage members only.
     */
    public function manageableRoles(): array
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => [self::ROLE_ADMIN, self::ROLE_MEMBER],
            self::ROLE_ADMIN => [self::ROLE_MEMBER],
            default => [],
        };
    }

    /**
     * Whether this admin may view or act on the given admin account.
     */
    public function canManage(Admin $target): bool
    {
        return $this->id !== $target->id
            && in_array($target->role, $this->manageableRoles(), true);
    }

    public function hasActivationTokenSent()
    {
        return !is_null($this->activation_token);
    }

    public static function generateUniqueUsername(string $name): string
    {
        $usernameBase = preg_replace('/\s+/', '_', strtolower($name));
        $username = $usernameBase . '_' . random_int(10000, 99999);
        $admins = Admin::where('username', $username)->get();
        while ($admins->count() > 0) {
            $username .= '_' . random_int(0, 9);
            $admins = Admin::where('username', $username)->get();
        }
        return $username;
    }
}