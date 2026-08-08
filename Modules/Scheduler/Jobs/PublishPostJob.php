<?php

declare(strict_types=1);

namespace Modules\Scheduler\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Scheduler\Services\PublishingService;

final class PublishPostJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(public int $postId, public ?int $postAccountId = null) {}

    public function handle(PublishingService $publishing): void
    {
        $publishing->publish($this->postId, $this->postAccountId);
    }
}
