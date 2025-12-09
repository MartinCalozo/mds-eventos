<?php

use App\Services\InvitationService;
use Mockery\MockInterface;

// Test para InvitationController (endpoint público)
it('returns invitation data from the controller', function () {

    // Simulamos el servicio externo
    $this->mock(InvitationService::class, function (MockInterface $mock) {
        $mock->shouldReceive('getInvitationByHash')
            ->once()
            ->with('abc123')
            ->andReturn([
                'success' => true,
                'invitation' => [
                    'full_name'  => 'Juan Perez',
                    'guest_count' => 3,
                ]
            ]);
    });

    $response = $this->getJson('/api/invitations/abc123');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'invitation' => [
                     'full_name' => 'Juan Perez',
                     'guest_count' => 3,
                 ]
             ]);
});

// Cuando el servicio devuelve error
it('returns error when invitation is not found', function () {

    $this->mock(InvitationService::class, function (MockInterface $mock) {
        $mock->shouldReceive('getInvitationByHash')
            ->once()
            ->with('wrong-hash')
            ->andReturn([
                'success' => false,
                'error'   => 'Invitation not found'
            ]);
    });

    $response = $this->getJson('/api/invitations/wrong-hash');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => false,
                 'error'   => 'Invitation not found'
             ]);
});
