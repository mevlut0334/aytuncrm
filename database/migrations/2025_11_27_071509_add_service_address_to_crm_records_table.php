<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_records', function (Blueprint $table) {
            $table->text('service_address')->nullable()->after('company_address');
        });
    }

    public function down(): void
    {
        Schema::table('crm_records', function (Blueprint $table) {
            $table->dropColumn('service_address');
        });
    }
};