<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\Core\Models\BaseModel;

final class SocialAccount extends BaseModel
{
    use HasFactory;

    protected $table = 'social_accounts';

    public const PLATFORM_FACEBOOK = 'facebook';

    public const PLATFORM_INSTAGRAM = 'instagram';

    public const PLATFORM_LINKEDIN = 'linkedin';

    public const PLATFORM_TWITTER = 'twitter';

    public const PLATFORM_PINTEREST = 'pinterest';

    public const PLATFORMS = [
        self::PLATFORM_FACEBOOK,
        self::PLATFORM_INSTAGRAM,
        self::PLATFORM_LINKEDIN,
        self::PLATFORM_TWITTER,
        self::PLATFORM_PINTEREST,
    ];

    protected $fillable = [
        'business_id',
        'platform',
        'account_name',
        'account_identifier',
        'avatar_url',
        'profile_url',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'scopes',
        'metadata',
        'is_connected',
        'connected_at',
        'last_synced_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'metadata' => 'array',
        'is_connected' => 'boolean',
        'token_expires_at' => 'datetime',
        'connected_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Business\Models\Business::class);
    }

    public function tokenExpiringSoon(int $days = 7): bool
    {
        return $this->token_expires_at !== null
            && $this->token_expires_at->isBefore(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at !== null && $this->token_expires_at->isPast();
    }

    public function platformLabel(): string
    {
        return Str::title($this->platform);
    }

    /**
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return \Modules\SocialAccounts\Database\Factories\SocialAccountFactory::new();
    }
}
