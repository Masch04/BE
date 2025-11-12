<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('slides')->delete();
        DB::table('slides')->insert([
            ['link_hinh_anh'=>'https://gardenviewcourt.com.vn/upload/files/chung/banner_rest.jpg', 'tinh_trang'=>'1'],
            ['link_hinh_anh'=>'https://emeraldbayhotelnhatrang.com/wp-content/uploads/2023/01/Banner-9-scaled.jpg', 'tinh_trang'=>'1'],
            ['link_hinh_anh'=>'https://gardenviewcourt.com.vn/upload/files/chung/banner_balcony.jpg', 'tinh_trang'=>'1'],
        ]);
    }
}
