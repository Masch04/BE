<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDichVu extends Model
{
    protected $table = 'chi_tiet_dich_vus';
    protected $fillable = ['id_hoa_don', 'id_dich_vu', 'don_gia'];

    public function dichVu()
    {
        return $this->belongsTo(DichVu::class, 'id_dich_vu');
    }

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'id_hoa_don');
    }
}