<?php

declare(strict_types=1);

namespace Modules\Scheduler\Console;

use Illuminate\Console\Command;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;
use Modules\Scheduler\Services\PublishingService;

final class PublishDuePostsCommand extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = 'Publish scheduled posts whose scheduled time has arrived';

    public function __construct(
        protected PostRepositoryInterface $posts,
        protected PublishingService $publishing,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $due = $this->posts->dueForPublishing();

        foreach ($due as $post) {
            $this->publishing->publish($post->getKey());
            $this->info("Published post #{$post->getKey()}.");
        }

        if ($due->isEmpty()) {
            $this->info('No posts due for publishing.');
        }

        return self::SUCCESS;
    }
}
