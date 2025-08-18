<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('lugar'); //Se puede cambiar a text el tipo de dato y cambiar el nombre del campo a 'ubicacion' si es necesario
            $table->string('asunto_audiencia'); // Asunto de la audiencia
            $table->text('descripcion')->nullable();
            $table->string('procedencia')->nullable();
            $table->date('fecha_audiencia');
            $table->time('hora_audiencia');
            $table->time('hora_fin_audiencia')->nullable();

            $table->unsignedBigInteger('area_id')->nullable();
            
            // Claves foráneas
            $table->foreignId('estatus_id')->constrained('estatus')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('c_area')->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiencias');
    }
}
