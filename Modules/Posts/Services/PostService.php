<?php

declare(strict_types=1);

namespace Modules\Posts\Services;

use Illuminate\Support\Facades\DB;
use Modules\Posts\Models\Post;
use Modules\Posts\Models\PostAccount;
use Modules\Posts\Repositories\Contracts\PostAccountRepositoryInterface;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;
use Modules\Scheduler\Services\FacebookPublisher;
use Modules\SocialAccounts\Models\SocialAccount;
use Throwable;

final class PostService
{
    public function __construct(
        protected PostRepositoryInterface $posts,
        protected PostAccountRepositoryInterface $postAccounts,
    ) {}

    public function create(int $businessId, int $userId, array $attributes, array $accountIds = []): Post
    {
        return DB::transaction(function () use ($businessId, $userId, $attributes, $accountIds) {
            $attributes['business_id'] = $businessId;
            $attributes['user_id'] = $userId;
            $attributes['status'] = $attributes['status'] ?? $this->statusFor($attributes);
            $attributes['content'] = $this->sanitizeContent($attributes['content'] ?? null);

            $post = $this->posts->create($attributes);

            $this->syncAccounts($post, $accountIds);

            return $post->load('accounts');
        });
    }

    public function update(Post $post, array $attributes, array $accountIds = []): Post
    {
        $attributes['content'] = $this->sanitizeContent($attributes['content'] ?? null);

        $this->posts->update($post->getKey(), $attributes);

        if ($accountIds) {
            $this->syncAccounts($post, $accountIds);
        }

        return $post->fresh('accounts');
    }

    public function cancel(int $businessId, int $postId): bool
    {
        $post = $this->posts->findForBusiness($businessId, $postId);

        if (! $post || ! $post->isScheduled()) {
            return false;
        }

        return (bool) $this->posts->update($postId, [
            'status' => Post::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function delete(int $businessId, int $postId): bool
    {
        $post = $this->posts->findForBusiness($businessId, $postId, ['accounts.socialAccount']);

        if ($post) {
            $this->deleteRemote($post);
        }

        return $this->posts->deleteForBusiness($businessId, $postId);
    }

    /**
     * Best-effort removal of the post from connected social platforms.
     */
    protected function deleteRemote(Post $post): void
    {
        foreach ($post->accounts as $delivery) {
            if ($delivery->status !== PostAccount::STATUS_PUBLISHED || ! $delivery->external_id) {
                continue;
            }

            $account = $delivery->socialAccount?->makeVisible(['access_token', 'refresh_token']);

            if ($account === null || $account->platform !== SocialAccount::PLATFORM_FACEBOOK || ! $account->access_token) {
                continue;
            }

            try {
                app(FacebookPublisher::class)->delete($delivery->external_id, $account);
            } catch (Throwable) {
                // Keep the local deletion even if the remote platform call fails.
            }
        }
    }

    /**
     * Mark a single platform delivery as failed.
     */
    public function markAccountFailed(PostAccount $postAccount, string $error): PostAccount
    {
        $updated = $this->postAccounts->update($postAccount->getKey(), [
            'status' => PostAccount::STATUS_FAILED,
            'error' => $error,
        ]);

        $this->recalculateStatus($postAccount->post_id);

        return $updated;
    }

    /**
     * Mark a single platform delivery as published.
     */
    public function markAccountPublished(PostAccount $postAccount, string $externalId): PostAccount
    {
        $updated = $this->postAccounts->update($postAccount->getKey(), [
            'status' => PostAccount::STATUS_PUBLISHED,
            'external_id' => $externalId,
            'published_at' => now(),
        ]);

        $this->recalculateStatus($postAccount->post_id);

        return $updated;
    }

    protected function syncAccounts(Post $post, array $accountIds): void
    {
        $this->postAccounts->forPost($post->getKey())->each->delete();

        foreach (array_unique(array_map('intval', $accountIds)) as $accountId) {
            $this->postAccounts->create([
                'post_id' => $post->getKey(),
                'social_account_id' => $accountId,
                'platform' => $this->platformFor($accountId),
                'status' => PostAccount::STATUS_PENDING,
            ]);
        }
    }

    protected function recalculateStatus(int $postId): void
    {
        $post = $this->posts->find($postId);

        if (! $post) {
            return;
        }

        $accounts = $this->postAccounts->forPost($postId);

        if ($accounts->isEmpty()) {
            return;
        }

        $published = $accounts->where('status', PostAccount::STATUS_PUBLISHED)->count();
        $failed = $accounts->where('status', PostAccount::STATUS_FAILED)->count();

        $status = match (true) {
            $failed === $accounts->count() => Post::STATUS_FAILED,
            $published === $accounts->count() => Post::STATUS_PUBLISHED,
            $published > 0 => Post::STATUS_PARTIAL,
            default => Post::STATUS_SCHEDULED,
        };

        $this->posts->update($postId, ['status' => $status, 'published_at' => $published > 0 ? now() : null]);
    }

    protected function statusFor(array $attributes): string
    {
        return ! empty($attributes['scheduled_at']) ? Post::STATUS_SCHEDULED : Post::STATUS_DRAFT;
    }

    protected function sanitizeContent(?string $content): ?string
    {
        if ($content === null || trim($content) === '') {
            return null;
        }

        return app(HtmlSanitizer::class)->sanitize($content);
    }

    protected function platformFor(int|string $accountId): string
    {
        $account = SocialAccount::find((int) $accountId);

        return $account?->platform ?? 'unknown';
    }
}
