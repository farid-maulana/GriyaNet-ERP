<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
          'created_at' => Carbon::now(),
          'updated_at' => Carbon::now(),
          'name' => $this->faker->company(),
          'address' => $this->faker->address(),
        ];
    }
}
