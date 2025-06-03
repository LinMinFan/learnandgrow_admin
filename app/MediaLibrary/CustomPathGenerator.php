<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        // 取得關聯的 MediaFolder 模型
        $model = $media->model;
        
        if ($model instanceof \App\Models\MediaFolder) {
            // 使用資料夾的 path 屬性作為儲存路徑
            return $model->path . '/';
        }
        
        // 如果不是 MediaFolder，回退到預設行為
        return $media->id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        // 縮圖也使用相同的路徑結構，但加上 conversions 子資料夾
        $model = $media->model;
        
        if ($model instanceof \App\Models\MediaFolder) {
            return $model->path . '/conversions/';
        }
        
        return $media->id . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        // 響應式圖片也使用相同的路徑結構，但加上 responsive-images 子資料夾
        $model = $media->model;
        
        if ($model instanceof \App\Models\MediaFolder) {
            return $model->path . '/responsive-images/';
        }
        
        return $media->id . '/responsive-images/';
    }
}