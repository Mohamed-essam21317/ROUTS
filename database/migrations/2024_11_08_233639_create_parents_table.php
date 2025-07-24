<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ربط بولي الأمر
            $table->string('phone')->unique();
            $table->string('address')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('dob');
            $table->string('profile_picture')->nullable();
            $table->string('fcm_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
