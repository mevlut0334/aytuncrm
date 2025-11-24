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
            'name' => 'Super Admin',
            'email' => 'superadmin@crm.com',
            'password' => '270317', // Otomatik hash'lenecek
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Test kullanıcısı
        User::create([
            'name' => 'Test User',
            'email' => 'test@crm.com',
            'password' => '270317',
            'role' => 'user',
            'is_active' => true,
        ]);
    }
}