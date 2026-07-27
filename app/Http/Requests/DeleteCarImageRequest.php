<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCarImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware admin di route
    }

    public function rules(): array
    {
        return [
            // URL/path gambar persis seperti yang tersimpan di kolom galeri
            'path' => ['required', 'string'],
        ];
    }
}
