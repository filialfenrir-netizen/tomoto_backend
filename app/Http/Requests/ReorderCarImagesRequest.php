<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCarImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware admin di route
    }

    public function rules(): array
    {
        return [
            // Array URL/path gambar dalam urutan baru hasil drag & drop
            'galeri' => ['required', 'array', 'min:1'],
            'galeri.*' => ['required', 'string'],
        ];
    }
}
