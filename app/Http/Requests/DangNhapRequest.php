<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class DangNhapRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'          => "required|email|exists:khach_hangs,email",
            'password'       => 'required',
        ];
    }
    public function messages()
    {
        return [
             'email.required'               => "Không được để trống email",
             'email.email'                  => "Phải nhập đúng định dạng email",
             'email.exists'                 => "Email không tồn tại trong hệ thống",
             'password.required'            => "Mật khẩu không được để trống",
        ];
    }
}
