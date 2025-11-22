<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_records', function (Blueprint $table) {
            $table->string('doctor_name')->nullable()->after('danger_level_id');
            $table->string('health_staff_name')->nullable()->after('doctor_id');
            $table->string('safety_expert_name')->nullable()->after('health_staff_id');
            $table->string('accountant_name')->nullable()->after('safety_expert_id');
        });
    }

    public function down(): void
    {
        Schema::table('crm_records', function (Blueprint $table) {
            $table->dropColumn(['doctor_name', 'health_staff_name', 'safety_expert_name', 'accountant_name']);
        });
    }
};