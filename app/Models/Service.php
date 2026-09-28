<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Service consulaire */
class Service extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    /** Champs traduits, dans l'ordre où ils s'affichent */
    public const TRANSLATED_FIELDS = [
        'description' => 'Description',
        'requirements' => 'Conditions',
        'documents' => 'Pièces à fournir',
        'fees' => 'Frais',
        'processing_time' => 'Délai de traitement',
        'office_hours' => 'Heures de dépôt',
    ];

    /** Icônes Phosphor proposées dans l'admin */
    public const ICONS = [
        'identification-card' => 'Passeport / identité',
        'airplane-tilt' => 'Visa',
        'stamp' => 'Légalisation',
        'baby' => 'État civil',
        'certificate' => 'Certificat',
        'users-three' => 'Diaspora',
        'briefcase' => 'Affaires',
        'file-text' => 'Document',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
