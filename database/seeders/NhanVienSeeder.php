<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhanVienSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('nhan_viens')->delete();
        DB::table('nhan_viens')->truncate();
        DB::table('nhan_viens')->insert([
            [
                'ma_nhan_vien'      =>  'NV001',
                'ho_va_ten'         =>  'Tuấn Cường',
                'ngay_sinh'         =>  '2000-01-01',
                'luong_co_ban'      =>  '10000000',
                'id_chuc_vu'        =>  '1',
                'ngay_bat_dau'      => '2024-01-01',
                'so_dien_thoai'     =>  '0357989225',
                'email'             =>  'tc091595@gmail.com',
                'password'          =>  bcrypt('123456'),
                'tinh_trang'        =>  1,
                'avatar'            =>  'https://i.pinimg.com/736x/cc/55/23/cc55235082b0cbee0f53587c11278fb2.jpg',
            ],
        ]);
    }
}
