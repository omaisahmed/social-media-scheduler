<?php

declare(strict_types=1);

namespace Modules\Scheduler\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\MediaLibrary\Models\MediaAsset;
use Modules\Posts\Models\Post;
use Modules\Posts\Services\PostMessageBuilder;
use Modules\SocialAccounts\Models\SocialAccount;
use RuntimeException;

/**
 * Publishes a post to a Facebook Page through the Graph API.
 *
 * Requires the connected social account to hold a Page access token
 * (with pages_manage_posts permission) and its account_identifier to be
 * the numeric Page ID.
 */
final class FacebookPublisher
{
    public function publish(Post $post, SocialAccount $account): string
    {
        $pageId = trim((string) $account->account_identifier);
        $token = (string) $account->access_token;

        if ($pageId === '') {
            throw new RuntimeException('Facebook account has no Page ID. Set the account identifier to your page id.');
        }

        if ($token === '') {
            throw new RuntimeException('Facebook account has no access token. Reconnect the page with a valid Page access token.');
        }

        $media = $post->featuredMedia;

        if ($media !== null && $media->isImage()) {
            $endpoint = "/{$pageId}/photos";
            $fields = ['message' => $this->message($post)];
            $file = ['source', Storage::disk($media->disk)->path($media->path), $media->original_name];
        } elseif ($media !== null && $media->type === MediaAsset::TYPE_VIDEO) {
            $endpoint = "/{$pageId}/videos";
            $fields = ['description' => $this->message($post)];
            $file = ['source', Storage::disk($media->disk)->path($media->path), $media->original_name];
        } else {
            $endpoint = "/{$pageId}/feed";
            $fields = ['message' => $this->message($post)];
            $file = null;
        }

        $response = $file !== null
            ? Http::attach($file[0], fopen($file[1], 'rb'), $file[2])
                ->post($this->url($endpoint), $fields + ['access_token' => $token])
            : Http::asForm()
                ->post($this->url($endpoint), $fields + ['access_token' => $token]);

        return $this->externalId($response);
    }

    public function delete(string $externalId, SocialAccount $account): void
    {
        $token = (string) $account->access_token;

        if ($token === '') {
            throw new RuntimeException('Facebook account has no access token.');
        }

        $response = Http::asForm()
            ->delete($this->url('/'.$externalId), ['access_token' => $token]);

        $data = $response->json();

        if ($response->failed() || ! is_array($data) || empty($data['success'])) {
            $error = $data['error']['message'] ?? $response->body();

            throw new RuntimeException('Facebook Graph API error: '.$error);
        }
    }

    protected function message(Post $post): string
    {
        $content = trim(app(PostMessageBuilder::class)->build($post, SocialAccount::PLATFORM_FACEBOOK));

        $tags = array_map(
            static fn (string $tag): string => '#'.ltrim($tag, '#'),
            $post->hashtagList(),
        );

        return $tags === [] ? $content : trim($content.' '.implode(' ', $tags));
    }

    protected function url(string $endpoint): string
    {
        return 'https://graph.facebook.com/'.config('services.facebook.graph_version').$endpoint;
    }

    protected function externalId(Response $response): string
    {
        $data = $response->json();

        if ($response->failed() || ! is_array($data)) {
            $error = $data['error']['message'] ?? $response->body();

            throw new RuntimeException('Facebook Graph API error: '.$error);
        }

        if (empty($data['id'])) {
            throw new RuntimeException('Facebook Graph API returned no post id.');
        }

        return (string) $data['id'];
    }
}
