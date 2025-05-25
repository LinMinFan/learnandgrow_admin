<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionGroupRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permission_groups,name',
                'regex:/^[a-z _-]+$/', // 只允許小寫英文字母、空格、底線、減號
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort' => 'nullable',
            'is_active' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '請輸入群組代號',
            'name.string' => '群組代號格式錯誤',
            'name.max' => '群組代號字數過長',
            'name.unique' => '群組代號已存在',
            'name.regex' => '群組代號格式錯誤',
            'display_name.required' => '請輸入群組名稱',
            'display_name.string' => '群組名稱錯誤',
            'display_name.max' => '群組名稱字數過長',
        ];
    }
}
