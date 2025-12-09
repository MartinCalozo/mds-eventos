<?php

use App\Models\Event;
use App\Models\InvitationRedemption;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Crea un evento
    $this->event = Event::create([
        'name' => 'Music Fest',
        'date' => '2025-05-10 20:00:00',
        'sector' => null
    ]);
});


it('redeems an invitation successfully', function () {

    Http::fake([
        '*' => Http::response([
            'success' => true,
            'data' => [
                'invitation_id' => 10,
                'event_id'      => $this->event->id,
                'event_name'    => 'Music Fest',
                'event_date'    => '2025-05-10 20:00:00',
                'guest_count'   => 2,
                'sector'        => null
            ]
        ], 200)
    ]);

    $response = $this->postJson('/api/redeem', [
        'hash' => 'SECRET'
    ]);

    $response->assertStatus(202)
             ->assertJson(['success' => true]);

    expect(InvitationRedemption::count())->toBe(1);
});



it('fails when hash was already redeemed', function () {

    InvitationRedemption::create([
        'invitation_id' => 10,
        'hash'          => 'SECRET',
        'event_id'      => $this->event->id,
        'guest_count'   => 1
    ]);

    $response = $this->postJson('/api/redeem', [
        'hash' => 'SECRET'
    ]);

    $response->assertStatus(400)
             ->assertJson([
                 'success' => false,
                 'error'   => 'Invitation already redeemed'
             ]);
});



it('fails when external API returns error', function () {

    Http::fake([
        '*' => Http::response([
            'success' => false,
            'message' => 'Invalid hash'
        ], 400)
    ]);

    // Hash debe ser válido (6 chars) para pasar RedeemRequest
    $response = $this->postJson('/api/redeem', [
        'hash' => 'ABC123'
    ]);

    $response->assertStatus(400)
             ->assertJson([
                 'success' => false,
                 'message' => 'Invalid hash'
             ]);
});
