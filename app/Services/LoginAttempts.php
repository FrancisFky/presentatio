<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Essais de mot de passe par compte, quelle que soit l'adresse IP : changer
 * d'adresse ne donne pas plus d'essais. Compté par adresse e-mail, que le
 * compte existe ou non, pour ne rien révéler.
 */
class LoginAttempts
{
    const MAX_FAILURES = 5;
    const LOCK_SECONDS = 900;

    public static function lockedMessage(?string $email): ?string
    {
        $key = self::key($email);

        if (!RateLimiter::tooManyAttempts($key, self::MAX_FAILURES)) {
            return null;
        }

        $minutes = max(1, (int) ceil(RateLimiter::availableIn($key) / 60));

        return "Trop d'essais de mot de passe pour ce compte. Réessayez dans {$minutes} minute(s) ou réinitialisez votre mot de passe.";
    }

    public static function failed(?string $email): void
    {
        RateLimiter::hit(self::key($email), self::LOCK_SECONDS);
    }

    public static function succeeded(?string $email): void
    {
        RateLimiter::clear(self::key($email));
    }

    private static function key(?string $email): string
    {
        return 'login-failures:' . sha1(mb_strtolower(trim((string) $email)));
    }
}
