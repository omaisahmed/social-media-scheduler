<?php

declare(strict_types=1);

namespace Modules\AI\Contracts;

interface ContentGenerator
{
    /**
     * Generate AI content based on the given context.
     *
     * @param  array{
     *     action: 'generate'|'rewrite'|'expand'|'shorten'|'hashtags',
     *     prompt: string,
     *     platform?: string,
     *     tone?: string,
     *     content?: string,
     *     language?: string,
     *     max_length?: int|null,
     *  }  $context
     */
    public function generate(array $context): string;

    public function supported(): bool;
}
