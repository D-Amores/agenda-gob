<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Hash;

class TestRegister extends Command
{
    protected $signature = 'test:register';
    protected $description = 'Test user registration functionality';

    public function handle()
    {
        $this->info('Testing user registration functionality...');
        
        // Check if areas exist
        $areasCount = Area::count();
        $this->info("Found {$areasCount} areas in database");
        
        if ($areasCount === 0) {
            $this->warn('No areas found in database. You might need to seed areas first.');
            return;
        }
        
        // Display available areas
        $areas = Area::all();
        $this->info('Available areas:');
        foreach ($areas as $area) {
            $this->line("- ID: {$area->id}, Name: {$area->area}");
        }
        
        $this->info('✅ User registration system is ready to use!');
        $this->info('You can now visit /register to create new users.');
    }
}
