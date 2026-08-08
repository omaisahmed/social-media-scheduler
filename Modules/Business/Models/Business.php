<?php

declare(strict_types=1);

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;

/**
 * A business is the tenant root. All domain data (posts, accounts, media,
 * analytics) hangs off a business. The authenticated user is scoped to their
 * primary business through the global HasBusiness scope.
 */
final class Business extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'businesses';

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'website_url',
        'industry',
        'description',
        'primary_timezone',
        'default_locale',
        'theme_color',
    ];

    protected $casts = [
        'primary_timezone' => 'string',
        'default_locale' => 'string',
        'theme_color' => 'string',
    ];

    /**
     * The tenant root is exempt from the business scope.
     */
    protected function hasBusinessScope(): bool
    {
        return false;
    }

    /**
     * The module keeps its factories inside the module, so point Eloquent
     * at the concrete factory class instead of the conventional location.
     *
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return \Modules\Business\Database\Factories\BusinessFactory::new();
    }

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(\Modules\Teams\Models\TeamMember::class);
    }

    public function isMember(\App\Models\User $user): bool
    {
        return $this->users()->whereKey($user->getKey())->exists();
    }
}
