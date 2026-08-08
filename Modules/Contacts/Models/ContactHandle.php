<?php

declare(strict_types=1);

namespace Modules\Contacts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

/**
 * A platform handle (username / uid / profile URL) for a contact.
 */
final class ContactHandle extends BaseModel
{
    protected $table = 'contact_handles';

    public const PLATFORM_FACEBOOK = 'facebook';

    public const PLATFORM_INSTAGRAM = 'instagram';

    public const PLATFORM_TWITTER = 'twitter';

    public const PLATFORM_LINKEDIN = 'linkedin';

    public const PLATFORMS = [
        self::PLATFORM_FACEBOOK,
        self::PLATFORM_INSTAGRAM,
        self::PLATFORM_TWITTER,
        self::PLATFORM_LINKEDIN,
    ];

    protected function hasBusinessScope(): bool
    {
        return false;
    }

    protected $fillable = [
        'contact_id',
        'platform',
        'handle',
        'platform_uid',
        'profile_url',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function handleAt(): string
    {
        return '@'.ltrim((string) $this->handle, '@');
    }

    public function platformLabel(): string
    {
        return ucfirst($this->platform);
    }
}
