<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePendingRegistrationRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'area_id' => ['required', 'exists:c_area,id'],
            'rol'     => 'required|string|exists:roles,name',
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
            'username.unique' => 'Usa otro nombre de usuario.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está siendo usado.',
            
            'area_id.required' => 'Debe seleccionar un área.',
            'area_id.exists' => 'El área seleccionada no es válida.',

            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',

            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres.',

            'rol.required' => 'El rol es obligatorio.',
            'rol.string' => 'El rol debe ser una cadena de texto.',
            'rol.exists' => 'El rol seleccionado no es válido.',
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
            'name' => 'nombre',
            'username' => 'nombre de usuario',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'area_id' => 'área',
            'rol' => 'rol',
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
        
        $response = response()->json([
            'success' => false,
            'message' => 'Error de validación.',
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
