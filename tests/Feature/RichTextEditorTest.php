<?php

declare(strict_types=1);

use Modules\Posts\Models\Post;
use Modules\Posts\Services\HtmlSanitizer;

test('sanitizer strips script tags and event handlers', function () {
    $sanitizer = app(HtmlSanitizer::class);

    $clean = $sanitizer->sanitize(
        '<p>Hello <script>alert(1)</script> <b onmouseover="alert(2)">world</b></p>',
    );

    expect($clean)->not->toContain('script')
        ->and($clean)->not->toContain('onmouseover')
        ->and($clean)->toContain('Hello')
        ->and($clean)->toContain('<b>world</b>');
});

test('sanitizer removes iframes and dangerous urls', function () {
    $sanitizer = app(HtmlSanitizer::class);

    $clean = $sanitizer->sanitize(
        '<p><a href="javascript:alert(1)">bad</a> <a href="https://example.com" target="_blank">good</a></p><iframe src="https://evil.com"></iframe>',
    );

    expect($clean)->not->toContain('javascript:')
        ->and($clean)->not->toContain('iframe')
        ->and($clean)->toContain('href="https://example.com"')
        ->and($clean)->toContain('rel="noopener noreferrer"');
});

test('sanitizer keeps supported rich text structure', function () {
    $sanitizer = app(HtmlSanitizer::class);

    $html = '<h2>Heading</h2><p>Para with <strong>bold</strong> and <em>italic</em>.</p><ul><li>Item</li></ul><blockquote>Quote</blockquote>';

    $clean = $sanitizer->sanitize($html);

    expect($clean)->toBe($html);
});

test('sanitizer preserves mention spans with their data attributes', function () {
    $sanitizer = app(HtmlSanitizer::class);

    $html = '<p>Hey <span class="ql-mention" data-id="42" data-value="Jane" data-denotation-char="@">@Jane</span>!</p>';

    $clean = $sanitizer->sanitize($html);

    expect($clean)->toContain('class="ql-mention"')
        ->and($clean)->toContain('data-id="42"')
        ->and($clean)->toContain('data-value="Jane"')
        ->and($clean)->toContain('data-denotation-char="@"')
        ->and($clean)->toContain('@Jane');
});

test('sanitizer strips data attributes from plain spans', function () {
    $clean = app(HtmlSanitizer::class)->sanitize(
        '<span class="foo" data-id="7" data-value="x">text</span>',
    );

    expect($clean)->toBe('<span>text</span>');
});

test('plain text content strips html for social platforms', function () {
    $post = new Post([
        'content' => '<p>Launch day! <strong>Big news</strong>.</p><p>Second line.</p>',
    ]);

    expect($post->plainTextContent())->toBe("Launch day! Big news.\nSecond line.");
});
