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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('registered_user_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->text('encrypted_payload');
            $table->timestamp('generation_date')->useCurrent();
            $table->timestamp('expiry_date')->nullable();
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
