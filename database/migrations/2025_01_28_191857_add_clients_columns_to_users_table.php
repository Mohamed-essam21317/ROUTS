<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddClientsColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
Schema::table('users', function (Blueprint $table) {
if (!Schema::hasColumn('users', 'role_based_id')) {
$table->string('role_based_id')->nullable();
}
if (!Schema::hasColumn('users', 'role')) {
$table->string('role')->nullable();
} 
if (!Schema::hasColumn('users', 'phone_number')) {
$table->string('phone_number')->nullable();
}
if (!Schema::hasColumn('users', 'national_id')) {
$table->string('national_id')->nullable();
}
if (!Schema::hasColumn('users', 'facebook_id')) {
$table->string('facebook_id')->nullable();
}
if (!Schema::hasColumn('users', 'google_id')) {
$table->string('google_id')->nullable();
}
if (!Schema::hasColumn('users', 'avatar')) {
$table->string('avatar')->nullable();
}
if (!Schema::hasColumn('users', 'otp')) {
$table->string('otp')->nullable();
}
if (!Schema::hasColumn('users', 'expires_at')) {
$table->timestamp('expires_at')->nullable();
}
if (!Schema::hasColumn('users', 'first_name')) {
$table->string('first_name')->nullable();
}
if (!Schema::hasColumn('users', 'last_name')) {
$table->string('last_name')->nullable();
}
if (!Schema::hasColumn('users', 'date_of_birth')) {
$table->date('date_of_birth')->nullable();
}
});

// نقل البيانات من clients إلى users
DB::table('users')->insertUsing(
[
'role_based_id',
'role',
'phone_number',
'national_id',
'facebook_id',
'google_id',
'avatar',
'otp',
'expires_at',
'first_name',
'last_name',
'date_of_birth',
],
DB::table('clients')->select(
'role_based_id',
'role',
'phone_number',
'national_id',
'facebook_id',
'google_id',
'avatar',
'otp',
'expires_at',
'first_name',
'last_name',
'date_of_birth'
)
);
}}
