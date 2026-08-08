<?php

declare(strict_types=1);

namespace Modules\Templates\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;

final class PostTemplate extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'post_templates';

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'content',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Business\Models\Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return \Modules\Templates\Database\Factories\PostTemplateFactory::new();
    }
}
