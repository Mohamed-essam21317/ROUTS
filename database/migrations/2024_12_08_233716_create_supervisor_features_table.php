<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorFeaturesTable extends Migration
{
    public function up()
    {
        Schema::create('supervisor_features', function (Blueprint $table) {
            $table->id();
            $table->string('supervisor_id'); // Foreign key referencing clients.role_based_id
            $table->foreign('supervisor_id')->references('role_based_id')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('report_id')->nullable();
            $table->integer('reports_handled')->default(0);
            $table->enum('emergency_status', ['Active', 'Resolved', 'None'])->default('None');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supervisor_features');
    }
}
