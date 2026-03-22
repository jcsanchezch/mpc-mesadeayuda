<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'      => ['required', 'string', 'max:200'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'adjuntos'    => ['nullable', 'array', 'max:20'],
            'adjuntos.*'  => ['file', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'      => 'El título es obligatorio.',
            'titulo.max'           => 'El título no puede superar los 200 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'adjuntos.max'         => 'Se puede adjuntar un máximo de 20 archivos.',
            'adjuntos.*.max'       => 'Cada archivo no puede superar los 20 MB.',
        ];
    }
}
