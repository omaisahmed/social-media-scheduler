<?php

declare(strict_types=1);

namespace Modules\AI\Services;

use Illuminate\Support\Facades\Http;
use Modules\AI\Contracts\ContentGenerator;

final class OpenAIContentGenerator implements ContentGenerator
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
        protected string $baseUrl,
    ) {
    }

    public function generate(array $context): string
    {
        $action = $context['action'];
        $prompt = $context['prompt'];
        $platform = $context['platform'] ?? null;
        $tone = $context['tone'] ?? 'professional';
        $content = $context['content'] ?? null;
        $maxLength = $context['max_length'] ?? null;

        $platformName = $platform ?? 'social media';

        $system = 'You are an expert social media copywriter. Produce engaging, platform-appropriate content. '
            .'Return only the content itself with no extra commentary, quotes, or explanation.';

        $user = match ($action) {
            'rewrite' => "Rewrite the following post in a {$tone} tone".($platform ? " for {$platform}" : '').".\n\n{$content}",
            'expand' => "Expand the following post into a longer {$platformName} post with more detail.\n\n{$content}",
            'shorten' => "Shorten the following post to its core message.\n\n{$content}",
            'hashtags' => "Suggest 5-10 relevant hashtags for the following post. Return only hashtags separated by spaces.\n\n{$content}",
            default => "Write a {$platformName} post in a {$tone} tone about: {$prompt}",
        };

        if ($maxLength) {
            $user .= "\n\nKeep the output under {$maxLength} characters.";
        }

        $response = Http::withToken($this->apiKey)
            ->baseUrl($this->baseUrl)
            ->asJson()
            ->timeout(30)
            ->post('/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
                'temperature' => 0.8,
            ])
            ->throw();

        return trim($response->json('choices.0.message.content', ''));
    }

    public function supported(): bool
    {
        return filled($this->apiKey);
    }
}
