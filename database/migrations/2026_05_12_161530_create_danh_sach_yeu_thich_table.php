<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_sach_yeu_thich', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguoi_dung');
            $table->unsignedBigInteger('id_phong');
            $table->timestamps();

            // Khai báo tường minh
            $table->foreign('id_nguoi_dung')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('id_phong')->references('id')->on('phong_tro')->onDelete('cascade');
            
            // Chống trùng lặp yêu thích
            $table->unique(['id_nguoi_dung', 'id_phong']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_sach_yeu_thich');
    }
};
