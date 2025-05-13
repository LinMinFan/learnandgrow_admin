<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'title', 
        'icon', 
        'route', 
        'parent_id', 
        'sort'
    ];

    /**
     * 子選單關聯
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort');
    }

    /**
     * 父選單關聯
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * 取得根選單（parent_id 為 null）
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort');
    }
}
