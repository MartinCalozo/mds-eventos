<?php

use App\Models\User;
use App\Models\Event;
use App\Models\InvitationRedemption;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Crear un usuario admin logueado
function actingAsAdmin() {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => Hash::make('secret123'),
    ]);

    return test()->actingAs($admin, 'api');
}

// Test: ticketsUsed()
it('returns used tickets for an event', function () {

    actingAsAdmin();

    $event = Event::factory()->create();

    $redemption = InvitationRedemption::factory()->create([
        'event_id' => $event->id,
    ]);

    // Ticket usado
    $ticketUsed = Ticket::factory()->create([
        'invitation_redemption_id' => $redemption->id,
        'used' => true,
        'validated_at' => now(),
    ]);

    // Ticket NO usado (no debe aparecer)
    Ticket::factory()->create([
        'invitation_redemption_id' => $redemption->id,
        'used' => false,
    ]);

    $response = $this->getJson("/api/admin/events/{$event->id}/tickets-used");

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true
             ])
             ->assertJsonStructure([
                'event',
                'data' => [
                    'data' => [
                        [
                            'tickets' => [
                                [
                                    'id',
                                    'code',
                                    'used',
                                    'validated_at'
                                ]
                            ]
                        ]
                    ]
                ]
             ]);

    expect($response->json('data.data.0.tickets'))->toHaveCount(1);
});

// Test: filtrar por fecha en ticketsUsed()
it('filters used tickets by date', function () {

    actingAsAdmin();

    $event = Event::factory()->create();

    InvitationRedemption::factory()->create([
        'event_id' => $event->id,
        'created_at' => '2024-01-10',
    ]);

    InvitationRedemption::factory()->create([
        'event_id' => $event->id,
        'created_at' => '2024-02-15',
    ]);

    $response = $this->getJson("/api/admin/events/{$event->id}/tickets-used?date=2024-01-10");

    $response->assertStatus(200);

    expect($response->json('data.data'))->toHaveCount(1);
});

// Test: redemptions() listado general
it('lists redemptions with filters', function () {

    actingAsAdmin();

    $eventA = Event::factory()->create(['sector' => 'VIP']);
    $eventB = Event::factory()->create(['sector' => 'GENERAL']);

    // Redención en EVENTO A
    InvitationRedemption::factory()->create([
        'event_id' => $eventA->id,
        'created_at' => '2024-03-20'
    ]);

    // Redención en EVENTO B
    InvitationRedemption::factory()->create([
        'event_id' => $eventB->id,
        'created_at' => '2024-03-20'
    ]);

    $response = $this->getJson("/api/admin/redemptions?event={$eventA->id}&sector=VIP");

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'filters' => [
                     'event' => $eventA->id,
                     'sector' => 'VIP'
                 ]
             ]);

    expect($response->json('data.data'))->toHaveCount(1);
});
