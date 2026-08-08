<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Business\Models\Business;
use Modules\Teams\Models\TeamMember;

#[Fillable([
    'name',
    'email',
    'password',
    'business_id',
    'timezone',
    'locale',
    'theme',
    'avatar_path',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'id', 'user_id')->where('business_id', $this->business_id);
    }

    public function role(): ?string
    {
        return $this->teamMember?->role;
    }

    public function isOwner(): bool
    {
        return $this->role() === 'owner';
    }

    public function hasRole(array|string $roles): bool
    {
        return in_array($this->role(), (array) $roles, true);
    }

    public function canManageTeam(?int $businessId = null): bool
    {
        return $this->hasRole(['owner', 'admin']);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function avatar(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=6366f1&color=fff';
    }
}
