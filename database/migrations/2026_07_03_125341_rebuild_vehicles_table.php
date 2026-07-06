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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_type', 'owner_id', 'department', 'phone', 'qr_code', 'status']);
            $table->foreignId('registered_user_id')->after('id')->constrained('registered_users')->onDelete('cascade');
            $table->date('registration_date')->nullable()->after('vehicle_type');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['registered_user_id']);
            $table->dropColumn(['registered_user_id', 'registration_date']);
            $table->string('owner_name')->nullable();
            $table->string('owner_type')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('department')->nullable();
            $table->string('phone')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('status')->nullable();
        });
    }
};
