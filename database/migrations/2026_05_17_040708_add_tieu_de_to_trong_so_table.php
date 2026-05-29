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
        Schema::table('trong_so_thuat_toan', function (Blueprint $table) {
            $table->string('tieu_de_hien_thi')->nullable()->after('ten_tieu_chi');
            $table->enum('loai_input', ['boolean', 'scale5', 'text', 'select'])->default('scale5')->after('tieu_de_hien_thi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trong_so_thuat_toan', function (Blueprint $table) {
            $table->dropColumn('tieu_de_hien_thi');
            $table->dropColumn('loai_input');
        });
    }
};
