<?php

declare(strict_types=1);

namespace Modules\Posts\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Core\Support\Timezone;
use Modules\Posts\Models\Post;

final class StorePostRequest extends FormRequest
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
            'account_ids' => ['required', 'array', 'min:1'],
            'account_ids.*' => ['integer', Rule::exists('social_accounts', 'id')->where('business_id', $businessId)],
        ];
    }

    /**
     * Compares the schedule time against "now" in the business timezone so
     * the naive datetime-local value is judged by the wall clock the user
     * actually picked.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $value = $this->input('scheduled_at');

            if ($value === null || $value === '') {
                return;
            }

            $timezone = Timezone::for((int) ($this->user()->business_id ?? 0));
            $scheduled = CarbonImmutable::parse($value, $timezone)->utc();

            if ($scheduled->lessThanOrEqualTo(CarbonImmutable::now('UTC'))) {
                $validator->errors()->add('scheduled_at', 'The schedule time must be in the future.');
            }
        });
    }
}
