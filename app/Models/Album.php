<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function cover()
    {
        // La photo mise en avant, sinon la plus récente
        return $this->hasOne(Photo::class)->ofMany(['is_featured' => 'max', 'id' => 'max']);
    }
}
