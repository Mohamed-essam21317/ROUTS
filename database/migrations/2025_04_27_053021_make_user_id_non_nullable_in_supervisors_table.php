<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeUserIdNonNullableInSupervisorsTable extends Migration
{
    public function up(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }
}

