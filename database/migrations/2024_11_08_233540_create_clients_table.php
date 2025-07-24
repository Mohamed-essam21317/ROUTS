<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('role_based_id')->unique(); // Unique column for roles
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Parent', 'Driver', 'Supervisor', 'School Admin']);
            $table->string('phone_number');
            $table->string('national_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
