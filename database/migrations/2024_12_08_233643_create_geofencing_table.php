<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeofencingTable extends Migration
{
    public function up()
    {
        Schema::create('geofencing', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // Link geofence to a student
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->decimal('latitude', 10, 8); // Latitude of the geofence center
            $table->decimal('longitude', 11, 8); // Longitude of the geofence center
            $table->integer('geofence_radius'); // Radius of the geofence in meters
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('geofencing');
    }
}
