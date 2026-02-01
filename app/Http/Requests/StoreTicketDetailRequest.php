<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'technical_data' => 'nullable|array',
            'details_text' => 'nullable|string',
            'status' => 'nullable|string',
        ];
    }
}
