<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('vehicle_type', 50)->nullable()->after('nim_nik');
            $table->string('vehicle_brand', 100)->nullable()->after('vehicle_type');
            $table->string('vehicle_model', 100)->nullable()->after('vehicle_brand');
            $table->string('vehicle_plate', 20)->nullable()->after('vehicle_model');
            $table->year('vehicle_year')->nullable()->after('vehicle_plate');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_type',
                'vehicle_brand',
                'vehicle_model',
                'vehicle_plate',
                'vehicle_year',
            ]);
        });
    }
};