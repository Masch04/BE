<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhachHangDoiMatKhauProfileRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'old_password' => 'required|min:6|max:30',
            'new_password'      => 'required|min:6|max:30',
            're_password'   => 'required|min:6|max:30|same:new_password',
        ];
    }


    public function messages(): array
    {
        return [
            'old_password.required'      => 'Mật khẩu cũ là bắt buộc.',
            'new_password.required'      => 'Mật khẩu mới là bắt buộc.',
            'new_password.min'           => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.max'           => 'Mật khẩu mới không được vượt quá 30 ký tự.',
            're_password.required'       => 'Vui lòng nhập lại mật khẩu.',
            're_password.same'           => 'Mật khẩu nhập lại không khớp.',
        ];
    }
}
