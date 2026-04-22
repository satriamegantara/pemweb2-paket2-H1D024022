<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ];
    }
}

