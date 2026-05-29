<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('so_dien_thoai')->nullable();
            $table->enum('vai_tro', ['admin', 'chu_tro', 'nguoi_tim_tro', 'cong_tac_vien'])->default('nguoi_tim_tro');
            $table->jsonb('khao_sat_loi_song')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
