<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['nullable', 'required_without:attachment', 'string', 'max:5000'],
            'attachment' => [
                'nullable',
                'required_without:body',
                File::types([
                    'jpg', 'jpeg', 'png', 'webp', 'gif',
                    'pdf', 'doc', 'docx', 'xls', 'xlsx',
                    'ppt', 'pptx', 'txt', 'csv', 'zip', 'rar',
                ])->max('10mb'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required_without' => 'Escribe un mensaje o selecciona un archivo.',
            'body.max' => 'El mensaje no puede superar los 5000 caracteres.',
            'attachment.required_without' => 'Escribe un mensaje o selecciona un archivo.',
            'attachment.max' => 'El archivo no puede superar los 10 MB.',
            'attachment.mimes' => 'El tipo de archivo seleccionado no está permitido.',
        ];
    }
}
