<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as spatieMedia;

class Media extends spatieMedia
{
    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    // 取得完整 URL
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
