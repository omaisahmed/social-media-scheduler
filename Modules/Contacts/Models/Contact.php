<?php

declare(strict_types=1);

namespace Modules\Contacts\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Contacts\Database\Factories\ContactFactory;
use Modules\Core\Models\BaseModel;

/**
 * A person (or organization) that can be mentioned in posts.
 *
 * A contact is platform-agnostic; the platform handles and numeric ids
 * live on the related {@see ContactHandle} rows, one per platform.
 */
final class Contact extends BaseModel
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'business_id',
        'name',
        'avatar_url',
    ];

    public function handles(): HasMany
    {
        return $this->hasMany(ContactHandle::class);
    }

    public function handleFor(string $platform): ?ContactHandle
    {
        return $this->handles->first(
            static fn (ContactHandle $handle): bool => $handle->platform === $platform,
        );
    }

    /**
     * The first non-empty handle across platforms, useful for display.
     */
    public function primaryHandle(): ?ContactHandle
    {
        return $this->handles->first(
            static fn (ContactHandle $handle): bool => (string) $handle->handle !== '',
        );
    }

    /**
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return ContactFactory::new();
    }
}
