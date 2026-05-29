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
        Schema::table('phong_tro', function (Blueprint $table) {
            $table->float('dien_tich')->nullable()->after('gia_phong')->comment('Diện tích phòng (m2)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phong_tro', function (Blueprint $table) {
            $table->dropColumn('dien_tich');
        });
    }
};
