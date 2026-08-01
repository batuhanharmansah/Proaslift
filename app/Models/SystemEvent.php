<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source',
        'type',
        'severity',
        'message',
        'stack_trace',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'created_at' => null,
    ];

    protected static function booted(): void
    {
        static::creating(function (SystemEvent $event) {
            $event->created_at ??= now();
        });
    }

    public static function log(string $source, string $type, string $severity, string $message, ?string $stackTrace = null, array $context = []): self
    {
        return static::create([
            'source' => $source,
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'stack_trace' => $stackTrace,
            'context' => $context,
        ]);
    }
}
