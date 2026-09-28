<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/** Page éditoriale fixe (À propos du Congo, de l'Ambassade, Investir au Congo) */
class Page extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
