<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
