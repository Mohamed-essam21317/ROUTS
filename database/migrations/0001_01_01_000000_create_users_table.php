<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('role_based_id')->default('1')->index(); // Unique ID for roles (driver, supervisor, etc.)
                // $table->string('supervisor_id')->nullable()->unique()->references('supervisor_id')->on('supervisor_features');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->default(''); // إضافة الحقل phone مع الفاصلة المنقوطة
                $table->string('fcm_token')->nullable()->comment('Firebase Cloud Messaging token'); // Add this line
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role')->default('user'); // Add the role column                $table->string('fcm_token')->nullable()->comment('Firebase Cloud Messaging token'); // Added FCM token field
                $table->unsignedBigInteger('school_id')->nullable()->constrained()->onDelete('set null');
                $table->string('facebook_id')->nullable();
                $table->string('google_id')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->engine = 'InnoDB';
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'facebook_id')) {
                    $table->string('facebook_id')->nullable();
                }
                if (!Schema::hasColumn('users', 'google_id')) {
                    $table->string('google_id')->nullable();
                }
            });
        }

        // Force table to be ready for references
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('REPAIR TABLE users');
        DB::statement('ANALYZE TABLE users');
        DB::statement('OPTIMIZE TABLE users');
        DB::commit();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
