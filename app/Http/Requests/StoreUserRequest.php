<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'exists:pending_registrations,id'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => 'El ID del registro pendiente es obligatorio.',
            'id.exists' => 'El registro pendiente no existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'id' => 'ID del registro pendiente',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->input('id') ?? $this->json('id')
        ]);

        // Honeypot
        if ($this->filled('website')) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'ok' => false,
                    'message' => 'Solicitud no válida.',
                    'errors' => ['security' => ['Solicitud no válida.']]
                ], 422)
            );
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        
        $response = response()->json([
            'ok' => false,
            'message' => 'Error de validación: ' . $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 422);
        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validaciones adicionales si es necesario
            if ($this->username && strlen($this->username) > 0) {
                $username = $this->username;
                if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
                    $validator->errors()->add('username', 'El nombre de usuario contiene caracteres no permitidos.');
                }
            }
        });
    }
}
