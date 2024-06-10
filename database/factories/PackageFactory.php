<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
          'created_at' => Carbon::now(),
          'updated_at' => Carbon::now(),
          'name' => $this->faker->regexify('Package \d{1}'),
          'speed' => $this->faker->numberBetween(5, 100),
          'price' => $this->faker->numberBetween(100000, 300000),
        ];
    }
}
