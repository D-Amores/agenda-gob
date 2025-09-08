<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estatus')->insert([
            ['estatus' => 'programado', 'created_at' => now(), 'updated_at' => now()],
            ['estatus' => 'reprogramado', 'created_at' => now(), 'updated_at' => now()],
            ['estatus' => 'atendido',     'created_at' => now(), 'updated_at' => now()],
            ['estatus' => 'cancelado',    'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}