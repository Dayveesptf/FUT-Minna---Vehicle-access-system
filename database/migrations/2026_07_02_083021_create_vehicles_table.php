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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->enum('owner_type', ['student', 'staff', 'visitor']);
            $table->string('owner_id')->nullable(); // staff/student ID
            $table->string('department')->nullable();
            $table->string('phone');
            $table->string('plate_number')->unique();
            $table->string('vehicle_brand');
            $table->string('vehicle_model');
            $table->string('vehicle_color');
            $table->string('vehicle_type'); // car, bike, van etc.
            $table->string('qr_code')->unique()->nullable();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
