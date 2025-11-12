<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createLoaiPhongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_loai_phong'        => 'required|min:5',
            'so_giuong'             => 'required|numeric|min:1',
            'so_nguoi_lon'          => 'required|numeric|min:1',
            'so_tre_em'             => 'required|numeric|min:0',
            'dien_tich'             => 'required|numeric|min:10',
            'hinh_anh'              => 'required',
            'tien_ich'              => 'required',
            'tinh_trang'            => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'ten_loai_phong.required' => 'Tên loại phòng không được để trống',
            'ten_loai_phong.min'      => 'Tên Loại Phòng Phải Từ 5 Ký Tự',
            'so_giuong.required'      => 'Không được để trống số giường',
            'so_giuong.numeric'       => 'Số giường phải là kiểu số',
            'so_giuong.min'           => 'Phải từ 1 giường trở lên',
            'so_nguoi_lon.required'   => 'Phải nhập số người lớn',
            'so_nguoi_lon.numeric'    => 'Số người lớn phải là kiểu số',
            'so_nguoi_lon.min'        => 'Người lớn tối thiểu phải là 1',
            'so_tre_em.required'      => 'Trẻ em không được để trống',
            'so_tre_em.numeric'       => 'Số trẻ em phải là kiểu số',
            'dien_tich.required'      => 'Diện tích không được để trống',
            'dien_tich.numeric'       => 'Diện tích phải là kiểu số',
            'dien_tich.min'           => 'Diện tích phải từ 10m vuông',
            'hinh_anh.required'       => 'Hình ảnh không được để trống',
            'tien_ich.required'       => 'Tiện ích không được để trống',
            'tinh_trang.required'     => 'Tình trạng không được để trống',
            'tinh_trang.boolean'      => 'Tình trạng chỉ có thể là Tạm Dừng hoặc Hoạt Động',
        ];
    }
}
