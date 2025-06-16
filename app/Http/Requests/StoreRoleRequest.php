<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\PermissionAuthorizer;

class StoreRoleRequest extends FormRequest
{
    use PermissionAuthorizer;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->authorizeByPermission('role');
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
            'permissions' => 'required|array|min:1',
            'permissions.*' => ['exists:permissions,id'],
        ];

        if ($this->isMethod('put')) {
            $rules['name'][] = Rule::unique('roles', 'name')->ignore($this->route('id'));
        } else {
            $rules['name'][] = 'unique:roles,name';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => '請輸入角色代號',
            'name.string' => '角色代號格式錯誤',
            'name.regex' => '角色代號格式錯誤',
            'name.max' => '角色代號過長',
            'name.unique' => '角色代號已存在',
            'display_name.required' => '請輸入角色名稱',
            'display_name.string' => '角色名稱格式錯誤',
            'display_name.max' => '角色名稱過長',
            'permissions.required' => '請選擇權限',
            'permissions.array' => '權限格式錯誤',
            'permissions.min' => '請至少選擇一項權限',
        ];
    }
}
