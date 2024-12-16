<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key referencing clients.id
            $table->foreign('user_id')->references('id')->on('clients')->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['Check-in/out', 'Delay', 'Weather', 'Traffic', 'Emergency']);
            $table->enum('status', ['Read', 'Unread'])->default('Unread');
            $table->boolean('log')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
