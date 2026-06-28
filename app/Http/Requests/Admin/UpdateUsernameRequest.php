<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUsernameRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules()
    {
        $userId = Auth::id();
        return [
            'username' => "required|string|max:100|unique:users,username,{$userId}",
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
        ];
    }
}
