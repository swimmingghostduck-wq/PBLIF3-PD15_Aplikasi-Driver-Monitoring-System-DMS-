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
        Schema::create('alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('monitoring_sessions')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->string('alert_type', 50); // 'kantuk', 'tidak_fokus', 'menguap', 'wajah_hilang'
            $table->string('severity', 20)->default('BAHAYA'); // 'BAHAYA', 'PERINGATAN'
            $table->float('duration_seconds')->default(1.5);
            $table->string('snapshot_path', 255)->nullable();
            $table->timestamp('timestamp')->useCurrent()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_logs');
    }
};
