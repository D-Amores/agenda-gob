<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        if ($this->has('rol')) {
            $this->merge([
                'rol' => strtolower($this->rol),
            ]);
        }
    }

    public function rules()
    {
        $user = $this->route('user');

        return [
            'username' => 'nullable|string|max:50|unique:users,username,'. $user->id,
            //'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            //'profile_photo'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:800',
            'rol' => 'required|string|exists:roles,name',
            'area_id' => 'required|exists:c_area,id',
        ];
    }

    public function messages()
    {
        return [
            'rol.required' => 'El rol es obligatorio.',
            'area_id.required' => 'El área es obligatoria.',
            //'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique'   => 'Usa otro nombre de usuario.',
            //'email.required'    => 'El correo electrónico es obligatorio.',
            //'email.email'       => 'Debes ingresar un correo electrónico válido.',
            //'email.unique'      => 'Este correo electrónico ya está en uso.',
            // 'profile_photo.image'=> 'El archivo debe ser una imagen.',
            // 'profile_photo.mimes'=> 'El formato permitido es JPG, JPEG, PNG o GIF.',
            // 'profile_photo.max'  => 'La imagen no puede superar 800KB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'ok' => false,
            'message' => 'Datos inválidos.',
            'errors' => $validator->errors()
        ], 422));
    }
}

