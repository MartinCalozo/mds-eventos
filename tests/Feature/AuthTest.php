<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('registers a checker user', function () {
    $response = $this->postJson('/api/auth/register-checker', [
        'name' => 'Tincho Calozo',
        'email' => 'checker@test.com',
        'password' => 'secret123'
    ]);
    
    $response->assertStatus(201)->assertJson(['success' => true]);

    expect(User::where('email', 'checker@test.com')->exists())->toBeTrue();
});
it('fails when name is missing on register', function () {

    $response = $this->postJson('/api/auth/register-checker', [
        'email' => 'noname@test.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422); // Laravel validation error
    $response->assertJsonValidationErrors(['name']);
});
it('fails when email is missing on register', function () {

    $response = $this->postJson('/api/auth/register-checker', [
        'name' => 'Tincho',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
it('fails when email already exists', function () {

    User::factory()->create(['email' => 'duplicate@test.com']);

    $response = $this->postJson('/api/auth/register-checker', [
        'name' => 'Tincho',
        'email' => 'duplicate@test.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
it('fails when password is missing', function () {
    $response = $this->postJson('/api/auth/register-checker', [
        'name' => 'Tincho',
        'email' => 'nopass@test.com'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});
it('fails when password is too short', function () {
    $response = $this->postJson('/api/auth/register-checker', [
        'name' => 'Tincho',
        'email' => 'shortpass@test.com',
        'password' => '123'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});

it('logs in a checker user', function () {

    $user = User::factory()->create([
        'email' => 'login@test.com',
        'password' => Hash::make('secret123'),
        'role' => 'checker',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'login@test.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

it('logs out successfully', function () {

    $user = User::factory()->create();

    $this->actingAs($user, 'api');

    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'message' => 'Sesión cerrada correctamente'
             ]);
});
