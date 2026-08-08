<?php

declare(strict_types=1);

namespace Modules\Posts\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\MediaLibrary\Models\MediaAsset;
use Modules\Posts\Http\Requests\StorePostRequest;
use Modules\Posts\Http\Requests\UpdatePostRequest;
use Modules\Posts\Models\Post;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;
use Modules\Posts\Services\PostService;
use Modules\Scheduler\Services\PublishingService;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;

final class PostController
{
    public function __construct(
        protected PostService $posts,
        protected PostRepositoryInterface $repository,
        protected SocialAccountRepositoryInterface $accounts,
        protected PublishingService $publishing,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'search']);

        return view('posts::index', [
            'posts' => $this->repository->paginateForBusiness($request->user()->business_id, $filters),
            'filters' => $filters,
            'statuses' => Post::STATUSES,
        ]);
    }

    public function create(Request $request): View
    {
        return view('posts::create', [
            'accounts' => $this->accounts->connectedForBusiness($request->user()->business_id),
            'media' => MediaAsset::query()
                ->where('business_id', $request->user()->business_id)
                ->where('type', MediaAsset::TYPE_IMAGE)
                ->latest()
                ->limit(24)
                ->get(),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $this->posts->create(
            $request->user()->business_id,
            $request->user()->getKey(),
            $request->safe()->except(['account_ids']),
            $request->input('account_ids', []),
        );

        if ($request->has('publish')) {
            $this->publishing->publish($post->getKey());

            return redirect()->route('posts.show', $post)->with('status', 'post-published');
        }

        return $post->isScheduled()
            ? redirect()->route('posts.index')->with('status', 'post-scheduled')
            : redirect()->route('posts.edit', $post)->with('status', 'post-saved');
    }

    public function show(Request $request, int $postId): View
    {
        $post = $this->repository->findForBusiness($request->user()->business_id, $postId, ['user', 'accounts.socialAccount', 'featuredMedia']);

        abort_unless($post, 404);

        return view('posts::show', ['post' => $post]);
    }

    public function edit(Request $request, int $postId): View
    {
        $post = $this->repository->findForBusiness($request->user()->business_id, $postId, ['accounts', 'featuredMedia']);

        abort_unless($post, 404);

        return view('posts::edit', [
            'post' => $post,
            'accounts' => $this->accounts->connectedForBusiness($request->user()->business_id),
            'media' => MediaAsset::query()
                ->where('business_id', $request->user()->business_id)
                ->where('type', MediaAsset::TYPE_IMAGE)
                ->latest()
                ->limit(24)
                ->get(),
        ]);
    }

    public function update(UpdatePostRequest $request, int $postId): RedirectResponse
    {
        $post = $this->repository->findForBusiness($request->user()->business_id, $postId);

        abort_unless($post, 404);

        $this->posts->update(
            $post,
            $request->safe()->except(['account_ids']),
            $request->input('account_ids', []),
        );

        if ($request->has('publish')) {
            $this->publishing->publish($postId);

            return redirect()->route('posts.show', $postId)->with('status', 'post-published');
        }

        return redirect()->route('posts.show', $postId)->with('status', 'post-updated');
    }

    public function destroy(Request $request, int $postId): RedirectResponse
    {
        $this->posts->delete($request->user()->business_id, $postId);

        return redirect()->route('posts.index')->with('status', 'post-deleted');
    }

    public function cancel(Request $request, int $postId): RedirectResponse
    {
        $this->posts->cancel($request->user()->business_id, $postId);

        return redirect()->route('posts.show', $postId)->with('status', 'post-cancelled');
    }

    public function publish(Request $request, int $postId): RedirectResponse
    {
        $post = $this->repository->findForBusiness($request->user()->business_id, $postId);

        abort_unless($post, 404);
        abort_if($post->isPublished() || $post->status === Post::STATUS_CANCELLED, 409);

        $this->publishing->publish($postId);

        return redirect()->route('posts.show', $postId)->with('status', 'post-published');
    }
}
