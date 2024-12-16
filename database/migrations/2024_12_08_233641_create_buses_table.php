<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusesTable extends Migration
{
    public function up()
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number')->unique(); // Unique identifier for each bus
            $table->string('driver_id'); // Match the type with clients.role_based_id
            $table->foreign('driver_id')->references('role_based_id')->on('clients')->onDelete('cascade');
            $table->string('supervisor_id')->nullable(); // Match the type with clients.role_based_id
            $table->foreign('supervisor_id')->references('role_based_id')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('route_id'); // Route relationship
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->integer('capacity');
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buses');
    }
}
