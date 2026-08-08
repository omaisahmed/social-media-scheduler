<?php

declare(strict_types=1);

namespace Modules\Scheduler\Services;

use Modules\Posts\Models\Post;
use Modules\Posts\Models\PostAccount;
use Modules\Posts\Repositories\Contracts\PostAccountRepositoryInterface;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;
use Modules\Posts\Services\PostService;
use Modules\SocialAccounts\Models\SocialAccount;
use Throwable;

/**
 * Drives the actual publish flow for a post and its delivery targets.
 *
 * Facebook posts go through the Graph API via the platform driver; other
 * platforms fall back to a simulation so the queue pipeline can still be
 * exercised end to end. Publish a specific account by passing
 * $postAccountId (retry path) or the whole post.
 */
final class PublishingService
{
    public function __construct(
        protected PostRepositoryInterface $posts,
        protected PostAccountRepositoryInterface $postAccounts,
        protected PostService $postService,
    ) {}

    public function publish(int $postId, ?int $postAccountId = null): void
    {
        $post = $this->posts->find($postId, ['business', 'featuredMedia']);

        if (! $post || $post->isPublished() || $post->status === Post::STATUS_CANCELLED) {
            return;
        }

        if ($postAccountId !== null) {
            $account = $this->postAccounts->find($postAccountId);

            if ($account && $account->post_id === $postId && $account->status === PostAccount::STATUS_PENDING) {
                $this->deliver($post, $account);
            }

            return;
        }

        foreach ($this->postAccounts->forPost($postId) as $account) {
            if ($account->status === PostAccount::STATUS_PENDING) {
                $this->deliver($post, $account);
            }
        }
    }

    protected function deliver(Post $post, PostAccount $postAccount): void
    {
        $account = $postAccount->socialAccount()->first()?->makeVisible(['access_token', 'refresh_token']);

        if ($account !== null && $account->platform === SocialAccount::PLATFORM_FACEBOOK) {
            $this->publishViaFacebook($post, $postAccount, $account);

            return;
        }

        // No real driver for this platform yet, so we simulate a delivery result.
        $succeeded = random_int(0, 9) !== 0;

        if ($succeeded) {
            $this->postService->markAccountPublished($postAccount, 'ext_'.uniqid());
        } else {
            $this->postService->markAccountFailed($postAccount, 'Simulated transient platform error.');
        }
    }

    protected function publishViaFacebook(Post $post, PostAccount $postAccount, SocialAccount $account): void
    {
        try {
            $externalId = app(FacebookPublisher::class)->publish($post, $account);

            $this->postService->markAccountPublished($postAccount, $externalId);
        } catch (Throwable $e) {
            $this->postService->markAccountFailed($postAccount, $e->getMessage());
        }
    }
}
