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
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mds.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Checker1',
            'email' => 'checker@mds.com',
            'password' => Hash::make('password'),
            'role' => 'checker',
        ]);

        Event::create([
            'name' => 'Music Fest',
            'date' => now()->addDays(7),
            'sector' => 'VIP',
        ]);

        Event::create([
            'name' => 'Food Festival',
            'date' => now()->addDays(10),
            'sector' => 'General',
        ]);
    }
}
