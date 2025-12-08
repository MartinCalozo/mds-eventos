<?php

use App\Services\InvitationService;

it('returns invitation data using fake API', function () {

    Http::fake([
        '*' => Http::response([
            'success' => true,
            'data' => [
                'invitation_id' => 1,
                'event_name' => 'Test Event',
                'event_date' => '2025-01-01 20:00:00',
                'guest_count' => 3,
                'sector' => 'VIP'
            ]
        ], 200)
    ]);

    $service = app(InvitationService::class);

    $result = $service->getInvitationByHash('ABC123');

    expect($result['success'])->toBeTrue();
    expect($result['data']['guest_count'])->toBe(3);
});