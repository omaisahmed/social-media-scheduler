<?php

declare(strict_types=1);

namespace Modules\Posts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\Core\Models\BaseModel;
use Modules\SocialAccounts\Models\SocialAccount;

final class PostAccount extends BaseModel
{
    protected $table = 'post_accounts';

    protected function hasBusinessScope(): bool
    {
        return false;
    }

    public const STATUS_PENDING = 'pending';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'post_id',
        'social_account_id',
        'platform',
        'status',
        'external_id',
        'error',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function platformLabel(): string
    {
        return Str::title($this->platform);
    }

    public function accountName(): string
    {
        return $this->socialAccount?->account_name ?? 'Account #'.$this->social_account_id;
    }
}
