<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Message envoyé par le formulaire de contact */
class Message extends Model
{
    protected $guarded = [];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public const STATUS_UNREAD = 'unread';
    public const STATUS_READ = 'read';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_UNREAD => 'Non lu',
        self::STATUS_READ => 'Lu',
        self::STATUS_ARCHIVED => 'Archivé',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_UNREAD);
    }

    public function markAsRead(): void
    {
        if ($this->status === self::STATUS_UNREAD) {
            $this->update(['status' => self::STATUS_READ, 'read_at' => now()]);
        }
    }
}
