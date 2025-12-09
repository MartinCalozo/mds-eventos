<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Usuario NO autenticado
it('blocks unauthenticated user with 401', function () {

    $response = $this->getJson('/api/checker/test'); // ruta real que usa role:checker

    $response->assertStatus(401)
             ->assertJson([
                 'success' => false,
                 'message' => 'Unauthenticated'
             ]);
});

// Usuario autenticado pero con rol incorrecto
it('blocks user with wrong role and returns 403', function () {

    $admin = User::factory()->create([
        'password' => Hash::make('password'),
        'role'     => 'admin'
    ]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/checker/test'); // requiere role=checker

    $response->assertStatus(403)
             ->assertJson([
                 'error' => 'Unauthorized - role required: checker'
             ]);
});

// Usuario con rol correcto
it('allows user with correct role', function () {

    $checker = User::factory()->create([
        'password' => Hash::make('password'),
        'role'     => 'checker'
    ]);

    $this->actingAs($checker, 'api');

    $response = $this->getJson('/api/checker/test');

    $response->assertStatus(200)
             ->assertSee('OK CHECKER');
});
