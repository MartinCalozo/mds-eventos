<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('blocks unauthenticated access and returns 401', function () {

    $response = $this->getJson('/api/admin/test');

    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Unauthenticated'
    ]);
});

it('blocks invalid token and returns 401', function () {

    $response = $this->getJson('/api/admin/test', [
        'Authorization' => 'Bearer token-invalido'
    ]);

    $response->assertStatus(401);
});

it('allows authenticated ADMIN user to access protected route', function () {

    $admin = User::factory()->create([
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/admin/test');

    $response->assertStatus(200);
});
