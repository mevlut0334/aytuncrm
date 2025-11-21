<?php

namespace Database\Seeders;

use App\Models\DangerLevel;
use Illuminate\Database\Seeder;

class DangerLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dangerLevels = [
            ['name' => 'Az Tehlikeli'],
            ['name' => 'Tehlikeli'],
            ['name' => 'Çok Tehlikeli'],
        ];

        foreach ($dangerLevels as $level) {
            DangerLevel::firstOrCreate(
                ['name' => $level['name']],
                $level
            );
        }

        $this->command->info('Danger levels seeded successfully!');
    }
}