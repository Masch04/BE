<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // THÊM DÒNG NÀY

class HoaDon extends Model
{
    use HasFactory;

    protected $table = 'hoa_dons';

    protected $fillable = [
        'ma_hoa_don',
        'id_khach_hang',
        'tong_tien',
        'is_thanh_toan',
        'ngay_den',
        'ngay_di'
    ];

    /**
     * Quan hệ với khách hàng
     * THÊM TOÀN BỘ HÀM NÀY VÀO CUỐI CLASS (trước dấu } cuối cùng)
     */
    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'id_khach_hang', 'id');
        //                    ↑ Model khách hàng      ↑ khóa ngoại     ↑ khóa chính
    }
}