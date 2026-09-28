<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
