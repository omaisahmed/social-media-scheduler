<?php

declare(strict_types=1);

namespace Modules\Scheduler\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Modules\Scheduler\Models\BestTimeWindow;

final class BestTimeService
{
    /**
     * Suggest the best future slot for a platform, preferring configured
     * best-time windows, otherwise falling back to the next business hour.
     */
    public function suggest(
        int $businessId,
        string $platform,
        ?CarbonInterface $after = null,
        int $daysAhead = 14,
    ): CarbonInterface {
        $after = $after ?? now();

        $windows = $this->windowsFor($businessId, $platform);

        if ($windows->isNotEmpty()) {
            foreach ($windows as $window) {
                $slot = $this->nextOccurrence($window, $after);

                if ($slot->greaterThanOrEqualTo($after)) {
                    return $slot;
                }
            }
        }

        return $after->copy()->addHours(24)->setMinutes(0)->setSeconds(0);
    }

    /**
     * @return Collection<int, BestTimeWindow>
     */
    protected function windowsFor(int $businessId, string $platform)
    {
        return BestTimeWindow::withoutBusinessScope(fn () => BestTimeWindow::query()
            ->where('business_id', $businessId)
            ->where('platform', $platform)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get());
    }

    protected function nextOccurrence(BestTimeWindow $window, CarbonInterface $after): CarbonInterface
    {
        $daysUntil = (7 + $window->day_of_week - $after->dayOfWeek) % 7;
        $slot = $after->copy()->addDays($daysUntil);
        [$startH, $startM] = array_map('intval', explode(':', $window->start_time));

        return $slot->setTime($startH, $startM);
    }
}
