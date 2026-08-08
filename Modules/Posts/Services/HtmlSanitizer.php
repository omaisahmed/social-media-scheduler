<?php

declare(strict_types=1);

namespace Modules\Posts\Services;

/**
 * Sanitizes rich-text HTML to a small allowlist of tags and attributes,
 * stripping scripts, event handlers and dangerous URL schemes. This is the
 * safety net that lets post content be rendered as trusted HTML.
 */
final class HtmlSanitizer
{
    /**
     * @var array<string, list<string>>
     */
    private const ALLOWED = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        's' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'blockquote' => [],
        'code' => [],
        'pre' => [],
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'h5' => [],
        'h6' => [],
        'span' => ['class', 'data-id', 'data-value', 'data-denotation-char'],
    ];

    /**
     * @var list<string>
     */
    private const STRIP_TAGS = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'button', 'svg', 'img'];

    public function sanitize(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        if (! $this->containsMarkup($html)) {
            return e($html);
        }

        $dom = new \DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><body>'.$html.'</body>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $dom->getElementsByTagName('body')->item(0);

        if ($body === null) {
            return e($html);
        }

        $this->cleanNode($body);

        $cleaned = '';

        foreach ($body->childNodes as $child) {
            $cleaned .= $dom->saveHTML($child);
        }

        return trim($cleaned);
    }

    protected function containsMarkup(string $html): bool
    {
        return preg_match('/<\s*[a-z]/i', $html) === 1;
    }

    protected function cleanNode(\DOMNode $node): void
    {
        if ($node instanceof \DOMElement) {
            $tag = strtolower($node->tagName);

            if (in_array($tag, self::STRIP_TAGS, true)) {
                $node->parentNode?->removeChild($node);

                return;
            }

            if (! in_array($tag, ['html', 'body'], true)) {
                if (! array_key_exists($tag, self::ALLOWED)) {
                    $this->unwrap($node);

                    return;
                }

                $this->stripAttributes($node, self::ALLOWED[$tag]);
            }
        }

        $children = [];

        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            $this->cleanNode($child);
        }
    }

    protected function unwrap(\DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    /**
     * @param  list<string>  $allowed
     */
    protected function stripAttributes(\DOMElement $element, array $allowed): void
    {
        $isMention = $element->tagName === 'span' && $element->getAttribute('class') === 'ql-mention';
        $attributes = [];

        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = trim((string) $attribute->nodeValue);

            if (! in_array($name, $allowed, true)) {
                continue;
            }

            if ($name === 'class' && $element->tagName === 'span' && $value !== 'ql-mention') {
                continue;
            }

            if (str_starts_with($name, 'data-') && ! $isMention) {
                continue;
            }

            if ($name === 'data-id' && preg_match('/^\d+$/', $value) !== 1) {
                continue;
            }

            if ($name === 'data-denotation-char' && ! in_array($value, ['@', '#'], true)) {
                continue;
            }

            if ($name === 'href' && ! $this->isSafeUrl($value)) {
                continue;
            }

            if ($name === 'target' && $value !== '_blank') {
                continue;
            }

            if ($name === 'rel' && $value !== 'noopener noreferrer') {
                continue;
            }

            $attributes[$name] = $attribute->nodeValue;
        }

        while ($element->attributes->length > 0) {
            $element->removeAttributeNode($element->attributes->item(0));
        }

        foreach ($attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }

        if ($element->tagName === 'a' && isset($attributes['target']) && $attributes['target'] === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    protected function isSafeUrl(string $url): bool
    {
        if ($url === '' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }
}
