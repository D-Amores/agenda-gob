<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Nombre de Ejemplo',
                'username' => 'adminGob',
                'email' => 'soporte.sistemas@anticorrupcionybg.gob.mx',
                'phone' => null,
                'profile_photo_path' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('admin12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
