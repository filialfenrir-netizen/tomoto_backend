<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetMainCarImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware admin di route
    }

    public function rules(): array
    {
        return [
            // Harus salah satu URL/path yang sudah ada di galeri
            'path' => ['required', 'string'],
        ];
    }
}
