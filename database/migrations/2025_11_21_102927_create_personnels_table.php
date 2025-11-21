<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Personel adı - arama için index
            $table->enum('role', ['doctor', 'health_staff', 'safety_expert', 'accountant'])
                  ->index(); // Personel türü - filtreleme için index
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->index(); // Email aramaları için index
            $table->timestamps();

            // Composite index: rol bazlı sıralama ve filtreleme için
            $table->index(['role', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};