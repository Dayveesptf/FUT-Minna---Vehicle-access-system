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
    Schema::create('registered_users', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('phone');
        $table->enum('user_category', ['student', 'staff', 'visitor']);
        $table->string('id_number')->nullable();
        $table->string('department')->nullable();
        $table->enum('status', ['active', 'suspended'])->default('active');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('registered_users');
}
};
