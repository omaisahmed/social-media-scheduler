<?php

declare(strict_types=1);

namespace Modules\Contacts\Services\Importers;

/**
 * The outcome of contacting a platform to discover mentionable accounts.
 *
 * @phpstan-type ImportedItem array{name: string, handle?: string|null, uid?: string|null, avatar?: string|null, profile_url?: string|null}
 */
final class ImportResult
{
    /**
     * @param  ImportedItem[]  $items
     * @param  string[]  $errors
     */
    public function __construct(
        public readonly array $items = [],
        public readonly array $errors = [],
    ) {}

    public function successful(): bool
    {
        return $this->errors === [];
    }
}
