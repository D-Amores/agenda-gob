<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->unsignedBigInteger('area_id');
            $table->string('verification_token', 64)->unique();
            $table->string('password'); // Contraseña ya hasheada
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('area_id')->references('id')->on('c_area');
            $table->index(['email', 'verification_token']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_registrations');
    }
}
