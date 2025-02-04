<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcademicYearAndAccessPointToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // إضافة العمودين
            $table->string('academic_year')->nullable();  // السنة الدراسية
            $table->string('access_point')->nullable();   // نقطة الوصول
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            // حذف العمودين في حالة التراجع عن الـ migration
            $table->dropColumn('academic_year');
            $table->dropColumn('access_point');
        });
    }
}
