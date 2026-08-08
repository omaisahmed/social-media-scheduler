<?php

declare(strict_types=1);

namespace Modules\Reports\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Core\Traits\HasBusiness;

final class ReportExport extends BaseModel
{
    use HasBusiness;

    public const TYPE_ANALYTICS = 'analytics';

    public const TYPE_CONTENT = 'content';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $table = 'report_exports';

    protected $fillable = [
        'business_id',
        'user_id',
        'type',
        'filters',
        'file_path',
        'status',
        'generated_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'generated_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Business\Models\Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function completed(): bool
    {
        return $this->status === self::STATUS_COMPLETED && $this->file_path;
    }
}
