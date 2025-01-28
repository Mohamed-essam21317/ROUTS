<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
/**
* Run the migrations.
*/
public function up(): void
{
Schema::table('clients', function (Blueprint $table) {
// تحقق من وجود الأعمدة قبل إضافتها
if (!Schema::hasColumn('clients', 'email')) {
$table->string('email')->nullable();
}

if (!Schema::hasColumn('clients', 'phone')) {
$table->string('phone')->nullable();
}

if (!Schema::hasColumn('clients', 'otp')) {
$table->string('otp');
}

if (!Schema::hasColumn('clients', 'expires_at')) {
$table->timestamp('expires_at');
}
});
}

/**
* Reverse the migrations.
*/
public function down(): void
{
Schema::table('clients', function (Blueprint $table) {
// حذف الأعمدة عند التراجع عن migration
$table->dropColumn(['email', 'phone', 'otp', 'expires_at']);
});
}
};
