<?php

declare(strict_types=1);

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Models\BaseModel;

final class AuditLog extends BaseModel
{
    public static bool $auditEnabled = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'business_id',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
