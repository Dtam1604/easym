<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xac_thuc_thuc_dia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_phong');
            $table->unsignedBigInteger('id_nguoi_xac_thuc');
            $table->jsonb('bao_cao_chi_tiet')->nullable();
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            $table->dateTime('ngay_thuc_hien')->nullable();
            $table->timestamps();

            // Khai báo tường minh
            $table->foreign('id_phong')->references('id')->on('phong_tro')->onDelete('cascade');
            $table->foreign('id_nguoi_xac_thuc')->references('id')->on('nguoi_dung')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xac_thuc_thuc_dia');
    }
};
