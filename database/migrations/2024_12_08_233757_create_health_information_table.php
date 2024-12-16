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
            $table->string('student_id'); // Referencing school-provided student ID
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->text('condition')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('health_information');
    }
}
