<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
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
                'regex:/^[a-z _-]+$/',
            ],
            'display_name' => 'required|string|max:200',
            'permission_group_id' => 'required|exists:permission_groups,id',
        ];
    
        if ($this->isMethod('put')) {
            $rules['name'][] = Rule::unique('permissions', 'name')->ignore($this->route('id'));
        } else {
            $rules['name'][] = 'unique:permissions,name';
        }
    
        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => '請輸入權限代稱',
            'name.unique' => '權限代稱已存在',
            'name.string' => '權限代稱格式錯誤',
            'name.regex' => '權限代稱格式錯誤',
            'name.max' => '權限代稱過長',
            'display_name.required' => '請輸入顯示名稱',
            'display_name.string' => '名稱格式錯誤',
            'display_name.max' => '名稱過長',
            'permission_group_id.required' => '請選擇群組',
            'permission_group_id.exists' => '選擇群組不存在',
        ];
    }
}
