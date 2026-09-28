<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;

/**
 * Ce que partagent les formulaires publics : la langue de la page, un piège
 * à robots et une limite d'envois par adresse IP.
 */
trait ProtectsPublicForms
{
    /** Les requêtes Livewire passent par /livewire/update, sans préfixe de langue */
    #[Locked]
    public string $locale = 'fr';

    /** Champ caché : un humain ne le voit pas, un robot le remplit */
    public string $website = '';

    // Protégées : une propriété publique serait modifiable depuis le navigateur
    protected int $maxAttempts = 5;
    protected int $decaySeconds = 600;

    public function mountProtectsPublicForms(): void
    {
        $this->locale = app()->getLocale();
    }

    public function hydrateProtectsPublicForms(): void
    {
        app()->setLocale($this->locale);
    }

    protected function isSpam(): bool
    {
        return filled($this->website);
    }

    /** Vrai (et message d'erreur) si cette IP a trop envoyé récemment */
    protected function tooManyAttempts(): bool
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts)) {
            return false;
        }

        $minutes = (int) ceil(RateLimiter::availableIn($this->throttleKey()) / 60);
        $this->addError('form', trans_choice('site.forms.too_many', $minutes, ['minutes' => $minutes]));

        return true;
    }

    protected function countAttempt(): void
    {
        RateLimiter::hit($this->throttleKey(), $this->decaySeconds);
    }

    private function throttleKey(): string
    {
        return 'site-form:' . static::class . ':' . request()->ip();
    }

    /** Un envoi d'e-mail raté ne doit jamais faire perdre la demande enregistrée */
    protected function sendSafely(callable $send): void
    {
        try {
            $send();
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
