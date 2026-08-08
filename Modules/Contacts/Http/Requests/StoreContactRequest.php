<?php

declare(strict_types=1);

namespace Modules\Contacts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreContactRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
            'handles' => ['nullable', 'array'],
            'handles.*.handle' => ['nullable', 'string', 'max:255'],
            'handles.*.platform_uid' => ['nullable', 'string', 'max:255'],
            'handles.*.profile_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
