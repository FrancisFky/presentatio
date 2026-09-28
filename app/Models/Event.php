<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    protected $casts = [
        'starts_on' => 'date',
    ];

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()->whereDate('starts_on', '>=', today())->orderBy('starts_on')->orderBy('starts_at');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->published()->whereDate('starts_on', '<', today())->orderByDesc('starts_on');
    }

    /** « 14:30 », sans les secondes */
    public function time(): ?string
    {
        return $this->starts_at ? substr($this->starts_at, 0, 5) : null;
    }
}
