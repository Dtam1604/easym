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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('mat_khau')->nullable()->change(); // Vì đăng nhập Google không cần mật khẩu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('google_id');
            // Reverting mat_khau to not null might fail if there are nulls, so better leave it or handle it carefully.
            // For this project, leaving it is safer.
        });
    }
};
