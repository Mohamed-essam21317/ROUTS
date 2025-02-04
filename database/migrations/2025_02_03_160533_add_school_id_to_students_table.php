<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id'); // هنا بنضيف العمود
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade'); // بنحدد العلاقة مع جدول schools
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['school_id']); // حذف العلاقة
            $table->dropColumn('school_id'); // حذف العمود
        });
    }

}
