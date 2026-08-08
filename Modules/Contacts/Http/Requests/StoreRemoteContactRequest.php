<?php

declare(strict_types=1);

namespace Modules\Contacts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Contacts\Models\ContactHandle;

final class StoreRemoteContactRequest extends FormRequest
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
            'platform' => ['required', 'in:'.implode(',', ContactHandle::PLATFORMS)],
            'name' => ['required', 'string', 'max:255'],
            'handle' => ['nullable', 'string', 'max:255'],
            'platform_uid' => ['required', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
