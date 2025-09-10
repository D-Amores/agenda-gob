<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'formValidationName'   => 'required|string|min:10|max:255',
            'asistenciaGobernador' => ['required', 'in:1,0'],
            'formValidationLugar'  => 'required|string|min:10|max:255',
            'formValidationFecha'  => 'required|date',
            'vestimenta'           => ['required', 'exists:vestimentas,id'],
            'hora_evento'          => 'required|date_format:H:i',
            'hora_fin_evento'      => 'required|date_format:H:i|after:hora_evento',
            //'estatus_id'           => 'required|exists:estatus,id',
            'descripcion'          => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'formValidationName.required' => 'El nombre es obligatorio.',
            'hora_fin_evento.after'       => 'La hora de fin debe ser posterior a la de inicio.',
        ];
    }
}
