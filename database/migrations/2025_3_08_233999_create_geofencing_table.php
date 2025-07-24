<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeofencingTable extends Migration
{
    public function up()
    {
        Schema::create('geofencing', function (Blueprint $table) {
            $table->id(); // Primary Key (Auto-increment)

            $table->string('name'); // Geofence name (e.g., "School Zone")

            $table->decimal('latitude', 10, 7); // Latitude of the geofence
            $table->decimal('longitude', 10, 7); // Longitude of the geofence
            $table->integer('radius')->default(500); // Radius in meters (default: 500m)

            $table->unsignedBigInteger('student_id')->nullable(); // Related student (if applicable)
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unsignedBigInteger('bus_id')->nullable(); // Related bus (optional)
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');

            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('geofencing');
    }
}
