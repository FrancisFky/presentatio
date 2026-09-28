<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Article d'actualité */
class News extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    protected $casts = [
        'published_on' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /** Publié, et pas programmé pour plus tard */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->published()->whereDate('published_on', '<=', today());
    }

    /** Un slug unique tiré du titre (français d'abord) */
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $i = 2;

        while (self::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
