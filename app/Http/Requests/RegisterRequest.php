<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'area_id' => ['required', 'exists:c_area,id'],
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
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.max' => 'El nombre de usuario no puede exceder 255 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            
            'area_id.required' => 'Debe seleccionar un área.',
            'area_id.exists' => 'El área seleccionada no es válida.',
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
            'username' => 'nombre de usuario',
            'email' => 'correo electrónico',
            'area_id' => 'área',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Verificar honeypot (anti-bot)
        if ($this->filled('website')) {
            if ($this->wantsJson()) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Solicitud no válida.',
                    'errors' => ['security' => ['Solicitud no válida.']]
                ], 422));
            }
            abort(422, 'Solicitud no válida.');
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
        if ($this->wantsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $validator->errors()
            ], 422);

            throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
        }

        parent::failedValidation($validator);
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
