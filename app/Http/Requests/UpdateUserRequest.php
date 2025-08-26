<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $user = $this->route('user'); // viene del parámetro de ruta {user}

        return [
            'username'       => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_photo'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:800', // ~800KB
        ];
    }
}
