<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AudienciaController extends Controller
{
    public function index(){
        return view('audiencia.registro');
    }
}
