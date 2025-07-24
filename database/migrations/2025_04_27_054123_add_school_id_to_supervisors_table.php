<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_school_id_to_supervisors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToSupervisorsTable extends Migration
{
    public function up()
    {
        Schema::table('supervisors', function (Blueprint $table) {
            // Add school_id column (as a foreign key)
            $table->unsignedBigInteger('school_id')->nullable()->after('email');

            // Optionally, add a foreign key constraint if you have a schools table
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('supervisors', function (Blueprint $table) {
            // Remove the foreign key constraint and the column
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
}
