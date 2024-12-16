<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('id'); // School-provided ID
            $table->primary('id'); // Set as primary key
            $table->string('name');
            $table->integer('age');
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('clients')->onDelete('cascade'); // Parent relationship
            $table->unsignedBigInteger('bus_id')->nullable();
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade'); // Bus relationship
            $table->text('health_info')->nullable();
            $table->enum('attendance_status', ['Checked-In', 'Checked-Out', 'Absent'])->default('Absent');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
