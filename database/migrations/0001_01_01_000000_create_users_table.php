<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            
            // Performans için index
            $table->index(['email', 'is_active']);
            $table->index('role');
        });

        // Password reset ve sessions tablolarını kaldırıyoruz (kullanmayacağız)
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};