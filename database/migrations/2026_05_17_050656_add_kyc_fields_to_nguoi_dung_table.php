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
            $table->boolean('da_xac_thuc_cccd')->default(false)->after('anh_dai_dien');
            $table->jsonb('thong_tin_cccd')->nullable()->after('da_xac_thuc_cccd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['da_xac_thuc_cccd', 'thong_tin_cccd']);
        });
    }
};
