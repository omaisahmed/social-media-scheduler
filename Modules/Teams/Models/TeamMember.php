<?php

declare(strict_types=1);

namespace Modules\Teams\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

final class TeamMember extends BaseModel
{
    use HasFactory;

    protected $table = 'team_members';

    public const ROLE_OWNER = 'owner';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MEMBER = 'member';

    public const ROLE_VIEWER = 'viewer';

    public const ROLES = [
        self::ROLE_OWNER,
        self::ROLE_ADMIN,
        self::ROLE_MEMBER,
        self::ROLE_VIEWER,
    ];

    protected $fillable = [
        'business_id',
        'user_id',
        'role',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Business\Models\Business::class);
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function canManageTeam(): bool
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_ADMIN], true);
    }

    /**
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return \Modules\Teams\Database\Factories\TeamMemberFactory::new();
    }
}
