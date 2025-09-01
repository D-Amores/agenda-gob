<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaForeignToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Si no existe la columna, la creamos
            if (!Schema::hasColumn('users', 'area_id')) {
                $table->unsignedBigInteger('area_id')->nullable()->after('id');
            }

            // Agregar la foreign key
            $table->foreign('area_id')
                  ->references('id')
                  ->on('c_area')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn('area_id');
        });
    }
}
