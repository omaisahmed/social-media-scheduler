<?php

declare(strict_types=1);

namespace Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Core\Traits\HasBusiness;

final class AnalyticsDaily extends BaseModel
{
    use HasBusiness;

    protected $table = 'analytics_daily';

    protected $fillable = [
        'business_id',
        'account_id',
        'platform',
        'date',
        'impressions',
        'reach',
        'engagements',
        'followers_delta',
        'raw',
    ];

    protected $casts = [
        'date' => 'date',
        'raw' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(\Modules\SocialAccounts\Models\SocialAccount::class, 'account_id');
    }

    public function engagementRate(): float
    {
        return $this->reach > 0 ? round(($this->engagements / $this->reach) * 100, 2) : 0.0;
    }
}
