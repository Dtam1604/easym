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
        Schema::create('loi_moi_o_ghep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguoi_gui');
            $table->unsignedBigInteger('id_nguoi_nhan');
            // cho_duyet, chap_nhan, tu_choi
            $table->string('trang_thai')->default('cho_duyet');
            $table->timestamps();

            $table->foreign('id_nguoi_gui')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('id_nguoi_nhan')->references('id')->on('nguoi_dung')->onDelete('cascade');
            
            // Đảm bảo không gửi trùng lời mời từ A đến B
            $table->unique(['id_nguoi_gui', 'id_nguoi_nhan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loi_moi_o_ghep');
    }
};
