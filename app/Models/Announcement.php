<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Communiqué ou avis officiel */
class Announcement extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    protected $casts = [
        'published_on' => 'date',
        'expires_on' => 'date',
        'is_pinned' => 'boolean',
    ];

    public const PRIORITIES = [
        'normal' => 'Normale',
        'important' => 'Importante',
        'urgent' => 'Urgente',
    ];

    /** Publié, commencé, et pas encore expiré */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->published()
            ->whereDate('published_on', '<=', today())
            ->where(fn ($q) => $q->whereNull('expires_on')->orWhereDate('expires_on', '>=', today()));
    }

    public function isExpired(): bool
    {
        return $this->expires_on !== null && $this->expires_on->isBefore(today());
    }
}
