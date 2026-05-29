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
        Schema::create('bao_cao_phong', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguoi_bao_cao');
            $table->unsignedBigInteger('id_phong');
            $table->string('ly_do');
            $table->text('chi_tiet')->nullable();
            $table->string('trang_thai')->default('chua_xu_ly'); // chua_xu_ly, dang_xem_xet, da_giai_quyet
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('id_nguoi_bao_cao')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('id_phong')->references('id')->on('phong_tro')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bao_cao_phong');
    }
};
