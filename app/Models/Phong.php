<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    use HasFactory;

    protected $table = 'phongs';

    protected $fillable = [
        'ten_phong',
        'gia_mac_dinh',
        'tinh_trang',
        'id_loai_phong',
        'tien_ich_khac',
    ];
    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'id_loai_phong');
    }
    // THAY ĐỔI: Một Phòng có thể xuất hiện trong nhiều Chi Tiết Thuê Phòng
    public function chiTietThuePhongs()
    {
        return $this->hasMany(ChiTietThuePhong::class, 'id_phong');
    }
}

