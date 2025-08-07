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
            $table->string('lugar');
            $table->text('descripcion')->nullable();
            $table->boolean('asistencia_de_gobernador');
            $table->dateTime('fecha_evento');
            $table->string('hora_evento');
            $table->timestamps();

            $table->unsignedBigInteger('area_id')->nullable();


            // Claves foráneas
            $table->foreignId('vestimenta_id')->constrained('vestimentas')->onDelete('cascade');
            $table->foreignId('estatus_id')->constrained('estatus')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            
            
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
