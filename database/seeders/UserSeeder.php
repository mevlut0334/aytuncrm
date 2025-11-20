<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin kullanıcı
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => '123456', // Otomatik hash'lenecek
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Test kullanıcısı
        User::create([
            'name' => 'Test User',
            'email' => 'test@crm.com',
            'password' => '123456',
            'role' => 'user',
            'is_active' => true,
        ]);
    }
}