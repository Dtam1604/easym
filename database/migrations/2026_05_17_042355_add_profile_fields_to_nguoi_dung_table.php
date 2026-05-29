<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('gioi_tinh', 10)->nullable()->after('khao_sat_loi_song'); // nam, nu, khac
            $table->integer('nam_sinh')->nullable()->after('gioi_tinh');
            $table->string('nghe_nghiep')->nullable()->after('nam_sinh');
            $table->string('anh_dai_dien')->nullable()->after('nghe_nghiep');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['gioi_tinh', 'nam_sinh', 'nghe_nghiep', 'anh_dai_dien']);
        });
    }
};
