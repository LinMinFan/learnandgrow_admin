<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:200',
            ],
            'email' => [
                'string',
                'email'
            ],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];

        if ($this->isMethod('put')) {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($this->route('id'));
            $rules['password'] = 'nullable|string|min:8|confirmed';
        } else {
            $rules['email'][] = 'unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => '請輸入帳號名稱',
            'name.string' => '帳號名稱格式錯誤',
            'name.max' => '帳號名稱過長',
            'email.required' => '請輸入Email信箱',
            'email.string' => 'Email信箱格式錯誤',
            'email.email' => 'Email信箱格式錯誤',
            'email.unique' => 'Email信箱已存在',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼最少8個字元',
            'password.confirmed' => '密碼確認不一致',
            'roles.array' => '角色必須是陣列格式',
            'roles.*.exists' => '所選角色不存在',
        ];
    }
}
