<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            if (!Schema::hasColumn('supervisors', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable() // Allow null values temporarily
                    ->constrained('users')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
