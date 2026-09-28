<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Demande de rendez-vous consulaire */
class Appointment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'preferred_date' => 'date',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_RESCHEDULED = 'rescheduled';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_DONE = 'done';

    public const STATUSES = [
        self::STATUS_PENDING => 'En attente',
        self::STATUS_CONFIRMED => 'Confirmé',
        self::STATUS_RESCHEDULED => 'À reprogrammer',
        self::STATUS_DECLINED => 'Refusé',
        self::STATUS_DONE => 'Honoré',
    ];

    /** Créneaux proposés sur le formulaire (heure de Nairobi) */
    public const TIME_SLOTS = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '14:00', '14:30', '15:00', '15:30', '16:00'];

    private const ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment) {
            $appointment->reference ??= self::newReference();
        });
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function serviceName(): string
    {
        return $this->service?->t('title') ?? $this->service_label ?? '—';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** « RDV-7K2Q9M » : court, lisible au téléphone, sans 0/O ni 1/I */
    public static function newReference(): string
    {
        do {
            $code = 'RDV-' . collect(range(1, 6))->map(fn () => self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)])->join('');
        } while (self::where('reference', $code)->exists());

        return $code;
    }
}
