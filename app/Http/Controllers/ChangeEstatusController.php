<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChangeEstatusRequest;
use App\Models\Audiencia;
use App\Models\Evento;

class ChangeEstatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ChangeEstatusRequest $request, $tipo, $id) // tipo puede ser 'audiencia' o 'evento' y $id es el ID de la audiencia o evento
    {   //El request valida que estatus_id sea enviado
        //Actualiza la audiencia o evento según el tipo
        try {
            if ($tipo === 'audiencia') {
                $modelo = Audiencia::findOrFail($id);
            } elseif ($tipo === 'evento') {
                $modelo = Evento::findOrFail($id);
            } else {
                return response()->json(['ok' => false, 'message' => 'Tipo no válido'], 400);
            }

            $modelo->estatus_id = $request->estatus_id;
            $modelo->save();

            return response()->json(['ok' => true, 'message' => 'Estatus actualizado']);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'Error al actualizar el estatus'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
