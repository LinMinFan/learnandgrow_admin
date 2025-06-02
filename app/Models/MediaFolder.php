<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFolder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'path',
        'name',
        'depth',
        'parent_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'depth' => 'integer',
    ];

    /**
     * 定義媒體集合 - 使用 folder-name-id
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->getCollectionName())
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'])
            ->useDisk('media')
            ->usePath($this->path); // 關鍵：指定儲存路徑;
    }

    /**
     * 定義媒體轉換（縮圖）
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')      // 定義一種名為 'thumb' 的轉換版本
            ->width(50)                         // 寬度設為 50px
            ->height(50)                        // 高度設為 50px
            ->sharpen(10)                        // 銳化圖片程度 10（讓圖片看起來更清晰）
            ->nonQueued();                       // 不走 queue，立即處理（開發時常用）
    }

    /**
     * 整合上傳流程
     */
    public function addFile($file)
    {
        return $this
            ->addMedia($file)
            ->toMediaCollection($this->getCollectionName());
    }

    /**
     * 取得資料夾內的媒體檔案
     */
    public function getFolderMedia()
    {
        return $this->getMedia($this->getCollectionName());
    }

    /**
     * 取得完整的 collection name
     */
    public function getCollectionName(): string
    {
        return 'folder-' . $this->name . '-' . $this->id;
    }

    /**
     * 取得完整的儲存路徑
     */
    public function getFullStoragePath(): string
    {
        return storage_path('app/public/upload/' . $this->path);
    }

    /**
     * 取得子資料夾
     */
    public function children(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'parent_id')->orderBy('name');
    }

    /**
     * 取得父資料夾
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    /**
     * 取得根層預設資料夾
     */
    public static function getDefaultFolders()
    {
        return self::where('is_default', true)
            ->where('depth', 0)
            ->orderBy('name')
            ->get();
    }

    /**
     * 根據 path 查找資料夾
     */
    public static function findByPath(string $path)
    {
        return self::where('path', $path)->first();
    }

    /**
     * 檢查是否為根層資料夾
     */
    public function isRootFolder(): bool
    {
        return $this->depth === 0 && $this->is_default;
    }

    /**
     * 取得資料夾內檔案數量
     */
    public function getMediaCount(): int
    {
        return $this->getMedia($this->getCollectionName())->count();
    }
}
