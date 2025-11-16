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
        Schema::create('chi_tiet_dich_vus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hoa_don');
            $table->unsignedBigInteger('id_dich_vu');
            $table->decimal('don_gia', 15, 2);     // giá tại thời điểm đặt
            $table->integer('so_luong')->default(1);
            $table->timestamps();

            $table->foreign('id_hoa_don')->references('id')->on('hoa_dons')->onDelete('cascade');
            $table->foreign('id_dich_vu')->references('id')->on('dich_vus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_dich_vus');
    }
};
