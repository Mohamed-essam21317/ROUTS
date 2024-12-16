<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('travel_history', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // Referencing school-provided student ID
            $table->unsignedBigInteger('bus_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_history');
    }
}
