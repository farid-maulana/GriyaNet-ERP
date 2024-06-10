<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        $branches = Branch::pluck('id')->toArray();

        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'phone_number' => $this->faker->regexify('08\d{10}'),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'address' => $this->faker->address(),
            'birthday' => $this->faker->date(),
            'hire_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'user_id' => User::factory(),
            'branch_id' => $this->faker->randomElement($branches),
        ];
    }
}
