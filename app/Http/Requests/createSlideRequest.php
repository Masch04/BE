<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createSlideRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'link_hinh_anh'        => 'required',
            'tinh_trang'            => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'link_hinh_anh.*'        => 'Hình Ảnh không được để trống',
            'tinh_trang.*'            => 'Tình trạng không được để trống',
        ];
    }
}
