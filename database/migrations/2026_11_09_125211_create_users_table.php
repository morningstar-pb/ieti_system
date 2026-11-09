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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique(); // This column is required
            $table->string('password');
            $table->enum('role', ['admin', 'standard'])->default('standard');
            $table->string('google_2fa_secret')->nullable();
            $table->boolean('is_2fa_enabled')->default(false);
            $table->string('profile_picture')->default('default.png');
            $table->string('theme')->default('light');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};