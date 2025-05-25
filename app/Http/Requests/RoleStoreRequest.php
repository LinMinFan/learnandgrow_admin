<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RoleStoreRequest extends FormRequest
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
        // 如果是編輯模式，則排除當前角色
        if ($this->isMethod('put')) {
            return [
                'name' => 'required|string|max:200|unique:roles,name,' . $this->route('id'),
                'permissions' => 'required|array|min:1',
            ];
        } else {
            // 如果是新增模式，則不排除
            return [
                'name' => 'required|string|max:200|unique:roles,name',
                'permissions' => 'required|array|min:1',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => '請輸入角色名稱',
            'name.string' => '角色名稱格式錯誤',
            'name.max' => '角色名稱過長',
            'name.unique' => '角色名稱已存在',
            'permissions.required' => '請選擇權限',
            'permissions.array' => '權限格式錯誤',
            'permissions.min' => '請至少選擇一項權限',
        ];
    }
}
