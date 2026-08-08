<?php

declare(strict_types=1);

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateBusinessRequest extends FormRequest
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
        $businessId = $this->route('business') ? $this->route('business')->getKey() : null;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:businesses,slug,'.$businessId],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'primary_timezone' => ['nullable', 'string', 'timezone', 'max:255'],
            'default_locale' => ['nullable', 'string', 'max:10'],
            'theme_color' => ['nullable', 'string', 'max:20'],
        ];
    }
}
