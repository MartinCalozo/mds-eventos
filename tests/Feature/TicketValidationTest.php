<?php

use App\Models\User;
use App\Models\Ticket;
use App\Models\InvitationRedemption;
use Laravel\Passport\Passport;

it('validates a ticket successfully', function () {

    // Crear usuario checker autenticado
    $user = User::factory()->create([
        'role' => 'checker'
    ]);

    // Autenticarlo en test sin usar oauth/token
    Passport::actingAs($user);

    // Crear redemption
    $redemption = InvitationRedemption::factory()->create();

    // Crear ticket
    $ticket = Ticket::factory()->create([
        'invitation_redemption_id' => $redemption->id
    ]);

    // Consumir endpoint protegido
    $response = $this->postJson("/api/tickets/{$ticket->code}/validate");

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'access'  => 'granted',
             ]);
});
it('rejects a ticket that was already used', function () {

    $user = User::factory()->create(['role' => 'checker']);
    Passport::actingAs($user);

    $redemption = InvitationRedemption::factory()->create();

    $ticket = Ticket::factory()->create([
        'invitation_redemption_id' => $redemption->id,
        'used' => true,
        'validated_by' => $user->id,
        'validated_at' => now()
    ]);

    $response = $this->postJson("/api/tickets/{$ticket->code}/validate");

    $response->assertStatus(400)
             ->assertJson([
                 'success' => false,
                 'error'   => 'Ticket already used',
             ]);
});

it('returns 404 if the ticket does not exist', function () {

    $user = User::factory()->create(['role' => 'checker']);
    Passport::actingAs($user);

    $invalidCode = "no-existe-123";

    $response = $this->postJson("/api/tickets/{$invalidCode}/validate");

    $response->assertStatus(404)
             ->assertJson([
                 'success' => false,
                 'error'   => 'Ticket not found',
             ]);
});
