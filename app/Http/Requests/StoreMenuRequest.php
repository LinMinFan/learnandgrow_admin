<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Traits\PermissionAuthorizer;

class StoreMenuRequest extends FormRequest
{
    use PermissionAuthorizer;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->authorizeByPermission('menu');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $menuId = $this->route('id') ?? null; // 取得更新時的 id（可能為 null）

        $rules = [
            'title' => [
                'required',
                'string',
                'max:200',
                Rule::unique('menus', 'title')->ignore($menuId),
            ],
            'icon' => [
                'nullable',
                'string',
            ],
            'route' => [
                Rule::requiredIf(fn() => !is_null($this->input('parent_id'))),
                'nullable',
                'string',
                Rule::unique('menus', 'route')
                    ->ignore($menuId)
                    ->whereNotNull('route'),
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:menus,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'permission_id' => [
                'nullable',
                'exists:permissions,id',
            ],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => '請輸入選單名稱',
            'title.string' => '選單格式錯誤',
            'title.max' => '選單名稱過長',
            'title.unique' => '選單已存在',
            'icon.string' => '圖示格式錯誤',
            'route.required' => '子選單的路由為必填',
            'route.string' => '路由格式錯誤',
            'route.unique' => '路由已存在',
            'parent_id.integer' => '父選單格式錯誤',
            'parent_id.exists' => '父選單不存在',
            'is_active.required' => '請輸入是否顯示',
            'is_active.boolean' => '顯示格式錯誤',
            'permission_id.exists' => '權限不存在',
        ];
    }
}
