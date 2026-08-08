<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\BaseModel;

final class MediaAsset extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    public const TYPE_AUDIO = 'audio';

    public const TYPE_DOCUMENT = 'document';

    protected $table = 'media_assets';

    protected $fillable = [
        'business_id',
        'user_id',
        'disk',
        'path',
        'thumb_path',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Business\Models\Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function thumbUrl(): string
    {
        if (! $this->thumb_path) {
            $this->generateThumbnail();
        }

        return $this->thumb_path
            ? Storage::disk($this->disk)->url($this->thumb_path)
            : $this->url();
    }

    public function generateThumbnail(): void
    {
        if (! $this->isImage()) {
            return;
        }

        $path = app(\Modules\MediaLibrary\Services\MediaLibraryService::class)->generateThumbnail($this->path);

        if ($path && $path !== $this->thumb_path) {
            $this->update(['thumb_path' => $path]);
        }
    }

    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    /**
     * @return Factory<static>
     */
    protected static function newFactory(): Factory
    {
        return \Modules\MediaLibrary\Database\Factories\MediaAssetFactory::new();
    }
}
