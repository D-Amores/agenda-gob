<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('lugar');//Se puede cambiar a text el tipo de dato y cambiar el nombre del campo a 'ubicacion' si es necesario
            $table->text('descripcion')->nullable();
            $table->boolean('asistencia_de_gobernador');
            $table->date('fecha_evento');
            $table->time('hora_evento');
            $table->time('hora_fin_evento')->nullable();
            

            $table->unsignedBigInteger('area_id')->nullable();


            // Claves foráneas
            $table->foreignId('vestimenta_id')->constrained('vestimentas')->onDelete('cascade');
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
        Schema::dropIfExists('eventos');
    }
}
