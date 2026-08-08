<?php

declare(strict_types=1);

namespace Modules\Posts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Posts\Models\Post;

final class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $businessId = (int) ($this->user()->business_id ?? 0);

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'hashtags' => ['nullable', 'string', 'max:500'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:'.implode(',', Post::STATUSES)],
            'featured_media_id' => ['nullable', 'integer', Rule::exists('media_assets', 'id')->where('business_id', $businessId)],
            'account_ids' => ['nullable', 'array', 'min:1'],
            'account_ids.*' => ['integer', Rule::exists('social_accounts', 'id')->where('business_id', $businessId)],
        ];
    }
}
