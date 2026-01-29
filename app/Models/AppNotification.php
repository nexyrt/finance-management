<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =====================================
    // SCOPES
    // =====================================

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // =====================================
    // HELPERS
    // =====================================

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function markAsRead(): bool
    {
        if ($this->isRead()) {
            return true;
        }
        return $this->update(['read_at' => now()]);
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'feedback_submitted' => 'chat-bubble-left-right',
            'feedback_responded' => 'chat-bubble-left-ellipsis',
            'feedback_status_changed' => 'arrow-path',
            default => 'bell',
        };
    }

    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'feedback_submitted' => 'blue',
            'feedback_responded' => 'green',
            'feedback_status_changed' => 'yellow',
            default => 'gray',
        };
    }

    // =====================================
    // FACTORY METHODS
    // =====================================

    public static function notify(int $userId, string $type, string $title, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function notifyMany(array $userIds, string $type, string $title, string $message, array $data = []): void
    {
        foreach ($userIds as $userId) {
            self::notify($userId, $type, $title, $message, $data);
        }
    }

    // =====================================
    // CLEANUP
    // =====================================

    public static function cleanupOld(int $days = 90): int
    {
        return self::where('created_at', '<', now()->subDays($days))
            ->whereNotNull('read_at')
            ->delete();
    }
}
