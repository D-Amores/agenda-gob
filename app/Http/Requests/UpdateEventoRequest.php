<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Formatea las horas antes de la validación
        if ($this->has('hora_evento') && $this->hora_evento) {
            $this->merge(['hora_evento' => substr($this->hora_evento, 0, 5)]);
        }

        if ($this->has('hora_fin_evento') && $this->hora_fin_evento) {
            $this->merge(['hora_fin_evento' => substr($this->hora_fin_evento, 0, 5)]);
        }
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
            'estatus_id'           => 'required|exists:estatus,id',
            'descripcion'          => 'nullable|string|max:500',
        ];
    }
}
