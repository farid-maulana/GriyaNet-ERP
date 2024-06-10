<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Menunggu Konfirmasi Admin',
                'position' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Menunggu Pembayaran',
                'position' => 2,
                'is_default' => false,
            ],
            [
                'name' => 'Menunggu Penjadwalan',
                'position' => 3,
                'is_default' => false,
            ],
            [
                'name' => 'Menunggu Pemasangan',
                'position' => 4,
                'is_default' => false,
            ],
            [
                'name' => 'Selesai',
                'position' => 5,
                'is_default' => false,
            ],
            [
                'name' => 'Area Tidak Tercover',
                'position' => 6,
                'is_default' => false,
            ],
        ];

        foreach ($stages as $stage) {
            PipelineStage::create($stage);
        }
    }
}
