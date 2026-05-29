<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trong_so_thuat_toan', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tieu_chi');
            $table->float('trong_so_nen')->default(1.0);
            $table->float('he_so_uu_tien')->default(1.5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trong_so_thuat_toan');
    }
};
