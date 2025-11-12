<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhachHangDatLaiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hash_reset'                   =>  'required|exists:khach_hangs,hash_reset',
            'password'                     => 'required|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/',
            're_password'                  =>  'required|same:password'
        ];
    }

    public function messages()
    {
        return [
            'hash_reset.required'          =>  'Mã kích hoạt yêu cầu phải nhập!',
            'hash_reset.exists'            =>  'Mã kích hoạt không tồn tại trong hệ thống!',
            'password.required'            =>  'Không được để trống mật khẩu',
            'password.regex'               =>  'Mật khẩu phải chứa ít nhất một chữ in hoa, một số, một ký tự đặc biệt và ít nhất 8 ký tự ',
            're_password.required'         =>  'Không được để trống nhập lại mật khẩu',
            're_password.same'             =>  'Nhập lại mật khẩu phải giống với mật khẩu'
        ];
    }
}
