<?php

declare(strict_types=1);

namespace Modules\Posts\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Business\Models\Business;
use Modules\Core\Casts\BusinessDateTime;
use Modules\Core\Models\BaseModel;
use Modules\MediaLibrary\Models\MediaAsset;
use Modules\Posts\Database\Factories\PostFactory;
use Modules\Posts\Services\HtmlSanitizer;

final class Post extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'posts';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SCHEDULED,
        self::STATUS_QUEUED,
        self::STATUS_PUBLISHED,
        self::STATUS_PARTIAL,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'business_id',
        'user_id',
        'title',
        'content',
        'hashtags',
        'status',
        'scheduled_at',
        'published_at',
        'cancelled_at',
        'source',
        'source_id',
        'featured_media_id',
    ];

    protected $casts = [
        'scheduled_at' => BusinessDateTime::class,
        'published_at' => BusinessDateTime::class,
        'cancelled_at' => BusinessDateTime::class,
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(PostAccount::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'featured_media_id');
    }

    /**
     * @return array<int, string>
     */
    public function hashtagList(): array
    {
        return array_values(array_filter(array_map(
            fn (string $tag) => trim($tag),
            preg_split('/\s+/', (string) $this->hashtags) ?: [],
        )));
    }

    /**
     * Mentions referenced by the rich-text content.
     *
     * @return array<int, array{id: int, value: string, denotation_char: string}>
     */
    public function mentions(): array
    {
        $content = (string) $this->content;

        if (preg_match_all('/<span\b[^>]*class="ql-mention"[^>]*>/i', $content, $spans) === 0) {
            return [];
        }

        $mentions = [];

        foreach ($spans[0] as $span) {
            preg_match('/data-id="(\d+)"/', $span, $id);
            preg_match('/data-value="([^"]*)"/', $span, $value);
            preg_match('/data-denotation-char="([^"]*)"/', $span, $char);

            if (! isset($id[1], $value[1])) {
                continue;
            }

            $mentions[] = [
                'id' => (int) $id[1],
                'value' => html_entity_decode($value[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'denotation_char' => $char[1] ?? '@',
            ];
        }

        return $mentions;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED && $this->scheduled_at !== null;
    }

    /**
     * Content that is safe to render as HTML.
     */
    public function renderableContent(): string
    {
        $content = (string) $this->content;

        if (trim($content) === '') {
            return '';
        }

        if (preg_match('/<\s*[a-z]/i', $content) !== 1) {
            return nl2br(e($content));
        }

        return app(HtmlSanitizer::class)->sanitize($content);
    }

    /**
     * Plain-text version of the content for social platform APIs.
     */
    public function plainTextContent(): string
    {
        $content = (string) $this->content;

        if (trim($content) === '') {
            return '';
        }

        $text = preg_replace('/<\/(p|div|h[1-6]|li|blockquote|pre)>/i', "\n", $content) ?? $content;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim((string) preg_replace('/[ \t]{2,}/', ' ', $text));
        $text = trim((string) preg_replace("/\n{3,}/", "\n\n", $text));

        return $text === '' ? '' : $text;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function canPublish(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_FAILED, self::STATUS_PARTIAL], true);
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    /**
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }
}
