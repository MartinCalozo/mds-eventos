<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\InvitationRedemption;
use App\Models\Event;

class InvitationRedemptionFactory extends Factory
{
    protected $model = InvitationRedemption::class;

    public function definition()
    {
        return [
            'invitation_id' => $this->faker->numberBetween(1, 999),
            'hash'          => strtoupper($this->faker->lexify('??????')),
            'event_id'      => Event::factory(),
            'guest_count'   => 1,
        ];
    }
}
