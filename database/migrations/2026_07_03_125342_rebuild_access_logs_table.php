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
        Schema::table('access_logs', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['officer_id']);
            $table->dropColumn(['vehicle_id', 'officer_id', 'entry_time', 'exit_time', 'gate', 'status', 'remarks']);
        });

        Schema::table('access_logs', function (Blueprint $table) {
            $table->foreignId('qr_code_id')->after('id')->constrained('qr_codes')->onDelete('cascade');
            $table->foreignId('gate_point_id')->constrained('gate_points')->onDelete('cascade');
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('scan_timestamp')->useCurrent();
            $table->enum('access_decision', ['granted', 'denied']);
            $table->enum('direction', ['in', 'out'])->nullable();
            $table->string('denial_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('access_logs', function (Blueprint $table) {
            $table->dropForeign(['qr_code_id', 'gate_point_id', 'operator_id']);
            $table->dropColumn(['qr_code_id', 'gate_point_id', 'operator_id', 'scan_timestamp', 'access_decision', 'direction', 'denial_reason']);
        });
    }
};
