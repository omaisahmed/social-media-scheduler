<?php

declare(strict_types=1);

namespace Modules\AI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AI\Contracts\ContentGenerator;

final class AiController
{
    public function __construct(protected ContentGenerator $generator)
    {
    }

    public function index(): View
    {
        return view('ai::compose');
    }

    public function generate(Request $request): JsonResponse
    {
        if (! $this->generator->supported()) {
            return response()->json(['message' => 'AI provider is not configured.'], 422);
        }

        $validated = $request->validate([
            'action' => ['required', 'in:generate,rewrite,expand,shorten,hashtags'],
            'prompt' => ['required_without:content', 'nullable', 'string', 'max:2000'],
            'platform' => ['nullable', 'string', 'max:60'],
            'tone' => ['nullable', 'string', 'max:60'],
            'content' => ['required_without:prompt', 'nullable', 'string', 'max:8000'],
            'max_length' => ['nullable', 'integer', 'between:1,10000'],
        ]);

        try {
            $content = $this->generator->generate($validated);

            return response()->json(['content' => $content]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'AI generation failed. Please try again.'], 502);
        }
    }
}
