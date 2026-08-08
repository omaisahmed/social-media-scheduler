<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\MediaLibrary\Models\MediaAsset;
use Modules\MediaLibrary\Services\MediaLibraryService;

final class MediaLibraryController
{
    public function __construct(protected MediaLibraryService $library)
    {
    }

    public function index(Request $request): View
    {
        $query = MediaAsset::query()
            ->where('business_id', $request->user()->business_id)
            ->latest();

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($search = $request->query('search')) {
            $query->where('original_name', 'like', '%'.$search.'%');
        }

        return view('media-library::index', [
            'assets' => $query->paginate(24),
            'type' => $type,
            'search' => $search,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'files' => ['required', 'array', 'max:10'],
            'files.*' => ['required', 'file', 'max:'.MediaLibraryService::MAX_SIZE_KB, 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,mp3,wav,pdf,docx,txt,csv'],
        ]);

        $assets = $this->library->storeMany(
            $request->user()->business_id,
            $validated['files'],
            $request->user()->getKey(),
        );

        if ($request->wantsJson()) {
            return response()->json([
                'assets' => collect($assets)->map(fn (MediaAsset $asset) => [
                    'id' => $asset->getKey(),
                    'original_name' => $asset->original_name,
                    'url' => $asset->url(),
                    'thumb_url' => $asset->thumbUrl(),
                    'mime_type' => $asset->mime_type,
                    'type' => $asset->type,
                    'width' => $asset->width,
                    'height' => $asset->height,
                ])->values(),
            ], count($assets) > 0 ? 201 : 422);
        }

        return back()->with('status', count($assets) > 0 ? 'media-uploaded' : 'media-upload-failed');
    }

    public function destroy(Request $request, int $assetId): RedirectResponse
    {
        $asset = MediaAsset::query()
            ->where('business_id', $request->user()->business_id)
            ->findOrFail($assetId);

        $this->library->delete($asset);

        return back()->with('status', 'media-deleted');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assets' => ['required', 'array', 'max:100'],
            'assets.*' => ['integer'],
        ]);

        $assets = MediaAsset::query()
            ->where('business_id', $request->user()->business_id)
            ->whereIn('id', $validated['assets'])
            ->get();

        $deleted = $this->library->deleteMany($assets);

        return back()->with('status', $deleted > 0 ? 'media-deleted' : 'media-delete-failed');
    }
}
