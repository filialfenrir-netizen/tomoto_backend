<?php

namespace App\Http\Requests;

use App\Services\CarImageService;
use Illuminate\Foundation\Http\FormRequest;

class UploadCarImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware admin di route
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => [
                'required',
                'file',
                'image',
                CarImageService::allowedExtensionsRule(),
                CarImageService::maxFileKbRule(),
            ],
        ];
    }
}
