<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected $fakerManual;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->fakerManual = FakerFactory::create();
    }

    public function definition()
    {
        $faker = $this->fakerManual;

        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => 'checker',
        ];
    }
}
