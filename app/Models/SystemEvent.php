<?php

namespace App\Models;

use App\Support\SystemEventCategorizer;
use Illuminate\Database\Eloquent\Model;

class SystemEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source',
        'type',
        'category',
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
            $event->category ??= SystemEventCategorizer::categorize(
                $event->type,
                $event->source,
                $event->message,
                $event->stack_trace
            );
        });
    }

    public function categoryLabel(): string
    {
        return SystemEventCategorizer::label($this->category);
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
