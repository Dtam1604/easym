<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_hen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguoi_thue');
            $table->unsignedBigInteger('id_chu_tro');
            $table->unsignedBigInteger('id_phong');
            $table->dateTime('thoi_gian_hen');
            $table->enum('trang_thai_cuoc_hen', ['cho_duyet', 'da_duyet', 'da_huy'])->default('cho_duyet');
            $table->timestamps();

            // Khai báo tường minh
            $table->foreign('id_nguoi_thue')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('id_chu_tro')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('id_phong')->references('id')->on('phong_tro')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_hen');
    }
};
