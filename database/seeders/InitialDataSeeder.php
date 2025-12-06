<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Crear un administrador
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mds.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Crear un checker
        User::create([
            'name' => 'Juan Checker',
            'email' => 'checker@mds.com',
            'password' => Hash::make('password'),
            'role' => 'checker',
        ]);

        // Crear un evento de ejemplo
        Event::create([
            'name' => 'Festival de Música Rosario',
            'date' => '2026-12-10 21:00:00',
            'sector' => 'VIP',
        ]);
    }
}
