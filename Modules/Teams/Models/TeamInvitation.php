<?php

declare(strict_types=1);

namespace Modules\Teams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

final class TeamInvitation extends BaseModel
{
    use HasFactory;

    protected $table = 'team_invitations';

    protected $fillable = [
        'business_id',
        'invited_by',
        'email',
        'role',
        'token',
        'expires_at',
        'accepted_at',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Business\Models\Business::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && $this->revoked_at === null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
