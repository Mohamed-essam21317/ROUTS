<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthInformationTable extends Migration
{
    public function up()
    {
        Schema::create('health_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // Defines the foreign key
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('emergency_contact')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('health_information');
    }
}
