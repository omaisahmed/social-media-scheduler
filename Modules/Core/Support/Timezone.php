<?php

declare(strict_types=1);

namespace Modules\Core\Support;

use Illuminate\Support\Facades\Context;
use Modules\Business\Models\Business;

/**
 * Resolves the timezone that a business schedules content in.
 *
 * The business timezone (primary_timezone) is the source of truth for the
 * content calendar. It falls back to the configured app timezone when no
 * business is available (for example in console commands).
 */
final class Timezone
{
    /** @var array<int, string> */
    protected static array $cache = [];

    public static function for(?int $businessId = null): string
    {
        $businessId ??= Context::get('business_id') ?? auth()->user()?->business_id;

        if ($businessId !== null) {
            return self::$cache[$businessId] ??= (Business::withoutBusinessScope(fn () => Business::find($businessId))?->primary_timezone ?? config('app.timezone'));
        }

        return config('app.timezone');
    }

    public static function flush(): void
    {
        self::$cache = [];
    }
}
