<?php

declare(strict_types=1);

namespace Modules\Contacts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ImportContactsRequest extends FormRequest
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
            'account_id' => ['required', 'integer'],
        ];
    }
}
