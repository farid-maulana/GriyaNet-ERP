<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Package;
use App\Models\PipelineStage;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $sellers = Seller::pluck('id')->toArray();
        $packages = Package::pluck('id')->toArray();
        $stages = PipelineStage::pluck('id')->toArray();

        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
            'phone_number' => $this->faker->regexify('08\d{10}'),
            'address' => $this->faker->address(),
            'seller_id' => $this->faker->randomElement($sellers),
            'package_id' => $this->faker->randomElement($packages),
            'pipeline_stage_id' => $this->faker->randomElement($stages),
        ];
    }
}
