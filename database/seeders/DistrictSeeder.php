<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/districts.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('districts.json file not found!');
            return;
        }

        $districts = json_decode(File::get($jsonPath), true);

        foreach ($districts as $district) {
            District::firstOrCreate(
                [
                    'province_id' => $district['province_id'],
                    'name' => $district['name']
                ],
                [
                    'province_id' => $district['province_id'],
                    'name' => $district['name'],
                ]
            );
        }

        $this->command->info('Districts seeded successfully! Total: ' . count($districts));
    }
}