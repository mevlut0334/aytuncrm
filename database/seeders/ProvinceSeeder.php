<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/provinces.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('provinces.json file not found!');
            return;
        }

        $provinces = json_decode(File::get($jsonPath), true);

        foreach ($provinces as $province) {
            Province::firstOrCreate(
                ['plate_code' => $province['plate_code']],
                [
                    'name' => $province['name'],
                    'code' => $province['code'],
                    'plate_code' => $province['plate_code'],
                    'region' => $province['region'],
                    'status' => $province['status'] ?? true,
                ]
            );
        }

        $this->command->info('Provinces seeded successfully! Total: ' . count($provinces));
    }
}