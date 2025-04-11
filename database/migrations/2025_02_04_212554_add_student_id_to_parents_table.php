<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdToParentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // foreign key
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropForeign(['student_id']); 
            $table->dropColumn('student_id'); 
        });
    }
}
