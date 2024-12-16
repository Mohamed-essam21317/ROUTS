<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id'); // Foreign key referencing clients.role_based_id
            $table->foreign('sender_id')->references('role_based_id')->on('clients')->onDelete('cascade');
            $table->string('receiver_id'); // Foreign key referencing clients.role_based_id
            $table->foreign('receiver_id')->references('role_based_id')->on('clients')->onDelete('cascade');
            $table->text('content');
            $table->enum('message_type', ['Text', 'Alert'])->default('Text');
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
