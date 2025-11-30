<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dich_vus')->delete();
        DB::table('dich_vus')->insert([
            ['id' => '1' ,'ten_dich_vu' => 'Đưa đón sân bay', 'don_gia' => '500000', 'don_vi_tinh' => 'Dịch Vụ', 'ghi_chu' => 'Xe đưa đón tận nơi, phục vụ 24/7.', 'tinh_trang' => '1'],
            ['id' => '2' ,'ten_dich_vu' => 'Dọn phòng hàng ngày', 'don_gia' => '300000', 'don_vi_tinh' => 'Dịch Vụ', 'ghi_chu' => 'Dọn dẹp, thay khăn và sắp xếp phòng mỗi ngày.', 'tinh_trang' => '1'],
            ['id' => '3' ,'ten_dich_vu' => 'Giặt ủi', 'don_gia' => '200000 ', 'don_vi_tinh' => 'Dịch Vụ', 'ghi_chu' => 'Giặt, sấy và ủi quần áo nhanh chóng.', 'tinh_trang' => '1'],
            ['id' => '4' ,'ten_dich_vu' => 'Thuê nhiếp ảnh', 'don_gia' => ' 600000 ', 'don_vi_tinh' => 'Người', 'ghi_chu' => 'Chụp ảnh chuyên nghiệp theo yêu cầu.', 'tinh_trang' => '1'],
        ]);
    }
}
