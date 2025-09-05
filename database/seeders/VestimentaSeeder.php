<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VestimentasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vestimentas')->insert([
            ['tipo' => 'formal',     'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'casual',     'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'uniforme',   'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'deportivo',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}