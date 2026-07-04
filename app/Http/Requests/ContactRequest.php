<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:3000'],
            'privacy' => ['accepted'],
            // Honeypot: debe llegar vacío. Si tiene valor, es spam.
            'website' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Indica tu nombre.',
            'email.required' => 'Indica tu email.',
            'email.email' => 'El email no es válido.',
            'message.required' => 'Escribe tu mensaje.',
            'privacy.accepted' => 'Debes aceptar la política de privacidad.',
            'website.prohibited' => 'Se ha detectado actividad no permitida.',
        ];
    }
}
