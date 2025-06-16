<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\PermissionAuthorizer;

class StoreArticleRequest extends FormRequest
{
    use PermissionAuthorizer;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->authorizeByPermission('post');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('id') ?? null; // 取得更新時的 id（可能為 null）

        $rules = [
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'title' => [
                'required',
                'string',
            ],
            'slug' => [
                'required',
                'string',
                'max:200',
                Rule::unique('articles', 'slug')->ignore($articleId),
            ],
            'excerpt' => [
                'nullable',
                'string',
            ],
            'content' => [
                'required',
            ],
            'cover_image' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],
            'is_top' => [
                'required',
                'boolean',
            ],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required' => '請選擇文章分類',
            'category_id.exists' => '分類不存在',
            'title.required' => '請輸入文章標題',
            'title.string' => '文章標題格式錯誤',
            'slug.required' => '請輸入文章代號',
            'slug.string' => '文章代號格式錯誤',
            'slug.max' => '文章代號格式錯誤',
            'excerpt.string' => '文章摘要格式錯誤',
            'content.required' => '請輸入文章內容',
            'cover_image.string' => '文章主圖格式錯誤',
            'status.required' => '請選擇狀態',
            'status.in' => '狀態類別錯誤',
            'is_top.required' => '請選擇是否置頂',
            'is_top.boolean' => '是否置頂格式錯誤',
        ];
    }
}
