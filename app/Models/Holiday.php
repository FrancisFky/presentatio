<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Jour de fermeture de l'ambassade */
class Holiday extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()->whereDate('date', '>=', today())->orderBy('date');
    }
}
