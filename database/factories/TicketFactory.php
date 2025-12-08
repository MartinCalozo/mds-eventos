<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use App\Models\InvitationRedemption;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'invitation_redemption_id' => InvitationRedemption::factory(),
            'code' => (string) Str::uuid(),
            'used' => false,
            'validated_by' => null,
            'validated_at' => null,
        ];
    }
}
