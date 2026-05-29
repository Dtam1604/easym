<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phong_tro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_chu_tro');
            $table->string('tieu_de');
            $table->text('mo_ta')->nullable();
            $table->decimal('gia_phong', 15, 2);
            $table->string('dia_chi_chi_tiet')->nullable();
            $table->jsonb('anh_phong')->nullable();
            $table->jsonb('anh_phap_ly')->nullable();
            $table->integer('muc_do_xac_thuc')->default(1); // 1 = Chờ duyệt, 2 = Đã xác thực
            $table->integer('trang_thai_thue')->default(1); // 1 = Còn trống, 2 = Đã cho thuê
            $table->jsonb('khao_sat_loi_song_chu_tro')->nullable(); // Thêm cho matching
            $table->integer('dien_tich')->nullable();
            $table->string('gioi_tinh_cho_thue', 10)->default('Tat ca'); // Nam, Nu, Tat ca
            // Vị trí (GEOMETRY Point) với Gist Index
            $table->geometry('vi_tri', subtype: 'point', srid: 4326)->nullable()->spatialIndex();
            $table->timestamps();

            // Khai báo tường minh Khóa ngoại
            $table->foreign('id_chu_tro')->references('id')->on('nguoi_dung')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phong_tro');
    }
};
