<?php

declare(strict_types=1);

namespace Modules\Scheduler\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Business\Models\Business;
use Modules\Core\Models\BaseModel;

final class BestTimeWindow extends BaseModel
{
    protected $table = 'best_time_windows';

    public const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    protected $fillable = [
        'business_id',
        'platform',
        'day_of_week',
        'start_time',
        'end_time',
        'score',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'score' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function dayLabel(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }
}
