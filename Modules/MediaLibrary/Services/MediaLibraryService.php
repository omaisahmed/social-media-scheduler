<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\MediaLibrary\Models\MediaAsset;

final class MediaLibraryService
{
    public const MAX_SIZE_KB = 51200;

    public const THUMB_WIDTH = 400;

    public const THUMB_QUALITY = 82;

    public function __construct(protected string $disk = 'public')
    {
    }

    public function store(int $businessId, UploadedFile $file, ?int $userId = null): MediaAsset
    {
        $mime = $file->getMimeType() ?? 'application/octet-stream';
        $type = $this->resolveType($mime);

        $path = $file->store('media/'.$businessId, ['disk' => $this->disk]);
        $dimensions = $this->dimensions($path);

        $asset = MediaAsset::create([
            'business_id' => $businessId,
            'user_id' => $userId,
            'disk' => $this->disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'type' => $type,
        ]);

        if ($asset->isImage()) {
            $asset->update(['thumb_path' => $this->generateThumbnail($path)]);
        }

        return $asset;
    }

    /**
     * Generate a small square-ish preview thumbnail (WebP when possible) next to the original.
     */
    public function generateThumbnail(string $path): ?string
    {
        $disk = Storage::disk($this->disk);
        $fullPath = $disk->path($path);

        if (! is_file($fullPath)) {
            return null;
        }

        try {
            $source = match (mime_content_type($fullPath) ?: '') {
                'image/jpeg', 'image/jpg', 'image/pjpeg' => @imagecreatefromjpeg($fullPath),
                'image/png' => @imagecreatefrompng($fullPath),
                'image/gif' => @imagecreatefromgif($fullPath),
                'image/webp' => @imagecreatefromwebp($fullPath),
                default => false,
            };

            if ($source === false) {
                return null;
            }

            $width = imagesx($source);
            $height = imagesy($source);
            $ratio = min(1, self::THUMB_WIDTH / max(1, max($width, $height)));
            $thumbWidth = (int) max(1, round($width * $ratio));
            $thumbHeight = (int) max(1, round($height * $ratio));

            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

            $dirname = dirname($path).'/thumbs';
            $basename = pathinfo($path, PATHINFO_FILENAME);
            $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
            $thumbPath = $dirname.'/'.$basename.'.'.$extension;

            if (! $disk->exists($dirname)) {
                $disk->makeDirectory($dirname);
            }

            $temp = tempnam(sys_get_temp_dir(), 'smm-thumb');

            if ($extension === 'webp') {
                imagewebp($thumb, $temp, self::THUMB_QUALITY);
            } else {
                imagejpeg($thumb, $temp, self::THUMB_QUALITY);
            }

            $disk->put($thumbPath, file_get_contents($temp));

            imagedestroy($thumb);
            imagedestroy($source);
            @unlink($temp);

            return $thumbPath;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<int, MediaAsset>
     */
    public function storeMany(int $businessId, array $files, ?int $userId = null): array
    {
        $assets = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $assets[] = $this->store($businessId, $file, $userId);
            }
        }

        return $assets;
    }

    public function delete(MediaAsset $asset): bool
    {
        $disk = Storage::disk($asset->disk);

        if ($asset->thumb_path) {
            $disk->delete($asset->thumb_path);
        }

        $disk->delete($asset->path);

        return $asset->delete() !== false;
    }

    /**
     * @param iterable<MediaAsset> $assets
     */
    public function deleteMany(iterable $assets): int
    {
        $deleted = 0;

        foreach ($assets as $asset) {
            if ($this->delete($asset)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    protected function resolveType(string $mime): string
    {
        return match (true) {
            str_starts_with($mime, 'image/') => MediaAsset::TYPE_IMAGE,
            str_starts_with($mime, 'video/') => MediaAsset::TYPE_VIDEO,
            str_starts_with($mime, 'audio/') => MediaAsset::TYPE_AUDIO,
            default => MediaAsset::TYPE_DOCUMENT,
        };
    }

    /**
     * @return array{width: int|null, height: int|null}
     */
    protected function dimensions(string $path): array
    {
        $fullPath = Storage::disk($this->disk)->path($path);

        if (! str_starts_with(mime_content_type($fullPath) ?: '', 'image/')) {
            return ['width' => null, 'height' => null];
        }

        try {
            [$width, $height] = getimagesize($fullPath) ?: [null, null];

            return ['width' => $width, 'height' => $height];
        } catch (\Throwable) {
            return ['width' => null, 'height' => null];
        }
    }
}
