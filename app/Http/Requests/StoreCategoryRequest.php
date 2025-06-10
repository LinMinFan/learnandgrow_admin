<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class StoreCategoryRequest extends FormRequest
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
        $categoryId = $this->route('id') ?? null; // 取得更新時的 id（可能為 null）

        $rules = [
            'name' => [
                'required',
                'string',
            ],
            'slug' => [
                'required',
                'string',
                'max:200',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => [
                'nullable',
            ],
            'parent_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'sort_order' => [
                'nullable',
            ],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => '請輸入分類名稱',
            'name.string' => '分類名稱格式錯誤',
            'slug.required' => '請輸入分類代號',
            'slug.string' => '分類代號格式錯誤',
            'slug.max' => '分類代號過長',
            'slug.unique' => '分類代號已存在',
            'parent_id.required' => '父分類必選',
            'parent_id.integer' => '父分類格式錯誤',
            'parent_id.exists' => '父分類不存在',
            'is_active.required' => '請輸入是否顯示',
            'is_active.boolean' => '顯示格式錯誤',
        ];
    }
}
