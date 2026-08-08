<?php

declare(strict_types=1);

namespace Modules\Posts\Services;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\ContactHandle;
use Modules\Contacts\Repositories\Contracts\ContactRepositoryInterface;
use Modules\Posts\Models\Post;

/**
 * Serializes a post's rich-text content into the plain-text message format
 * expected by a given social platform, replacing @mention spans with the
 * platform's native tagging syntax where a matching contact handle exists.
 */
final class PostMessageBuilder
{
    public function __construct(protected ContactRepositoryInterface $contacts) {}

    public function build(Post $post, string $platform): string
    {
        $content = (string) $post->content;

        if (trim($content) === '') {
            return '';
        }

        $mentions = $post->mentions();

        if ($mentions !== []) {
            $content = $this->resolveMentions($post, $content, $platform);
        }

        return $this->toText($content);
    }

    protected function resolveMentions(Post $post, string $content, string $platform): string
    {
        $ids = array_unique(array_column($post->mentions(), 'id'));

        $contacts = $this->contacts
            ->findByIds((int) $post->business_id, $ids, ['handles'])
            ->keyBy('id');

        $callback = function (array $matches) use ($contacts, $platform): string {
            preg_match('/data-id="(\d+)"/', $matches[0], $id);
            preg_match('/data-value="([^"]*)"/', $matches[0], $value);

            $contact = isset($id[1]) ? $contacts->get((int) $id[1]) : null;
            $display = isset($value[1])
                ? html_entity_decode($value[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                : '';

            return $this->encodeMention($contact, $display, $platform);
        };

        return (string) preg_replace_callback(
            '/<span\b[^>]*class="ql-mention"[^>]*>.*?<\/span>/is',
            $callback,
            $content,
        );
    }

    protected function encodeMention(?Contact $contact, string $display, string $platform): string
    {
        $char = '@';
        $fallback = $char.$display;

        if ($contact === null) {
            return $display === '' ? '' : $fallback;
        }

        $handle = $contact->handleFor($platform);

        if ($handle === null) {
            return $fallback;
        }

        $uid = trim((string) $handle->platform_uid);
        $handleName = trim((string) $handle->handle);

        return match ($platform) {
            ContactHandle::PLATFORM_FACEBOOK => $uid !== ''
                ? "@[{$uid}:1:{$display}]"
                : $this->handleText($handleName, $char, $fallback),
            ContactHandle::PLATFORM_INSTAGRAM,
            ContactHandle::PLATFORM_TWITTER => $this->handleText($handleName, $char, $fallback),
            ContactHandle::PLATFORM_LINKEDIN => $uid !== ''
                ? "@[{$display}]({$uid})"
                : $this->handleText($handleName, $char, $fallback),
            default => $fallback,
        };
    }

    protected function handleText(string $handleName, string $char, string $fallback): string
    {
        return $handleName !== '' ? $char.ltrim($handleName, $char) : $fallback;
    }

    protected function toText(string $html): string
    {
        $text = preg_replace('/<\/(p|div|h[1-6]|li|blockquote|pre)>/i', "\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim((string) preg_replace('/[ \t]{2,}/', ' ', $text));

        return trim((string) preg_replace("/\n{3,}/", "\n\n", $text));
    }
}
