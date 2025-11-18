<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dich_vu_hoa_don', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hoa_don');
            $table->unsignedBigInteger('id_dich_vu');
            $table->integer('so_luong')->default(1);
            $table->bigInteger('thanh_tien'); // dùng bigInteger thay vì integer nếu giá lớn
            $table->timestamps();

            // Foreign key
            $table->foreign('id_hoa_don')->references('id')->on('hoa_dons')->onDelete('cascade');
            $table->foreign('id_dich_vu')->references('id')->on('dich_vus')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dich_vu_hoa_don');
    }
};