<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'name'   => $this->faker->sentence(3),
            'date'   => $this->faker->dateTime(),
            'sector' => null,
        ];
    }
}
