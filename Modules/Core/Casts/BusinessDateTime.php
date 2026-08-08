<?php

declare(strict_types=1);

namespace Modules\Core\Casts;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Modules\Core\Support\Timezone;

/**
 * Stores datetimes in UTC and surfaces them in the business timezone.
 *
 * Scheduled times are entered by users in the business's local wall clock,
 * converted to UTC for storage, and converted back to the business timezone
 * whenever they are read. This keeps comparisons made by the scheduler
 * (which runs in UTC) correct regardless of the tenant's timezone.
 */
final class BusinessDateTime implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?CarbonImmutable
    {
        if ($value === null) {
            return null;
        }

        return CarbonImmutable::parse($value, 'UTC')->setTimezone(Timezone::for((int) ($model->business_id ?? 0)));
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            $carbon = CarbonImmutable::instance($value);
        } else {
            $carbon = CarbonImmutable::parse($value, Timezone::for((int) ($model->business_id ?? 0)));
        }

        return $carbon->setTimezone('UTC')->format('Y-m-d H:i:s');
    }
}
