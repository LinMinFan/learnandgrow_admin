<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    protected $fillable = [
        'path',
        'name',
        'depth',
        'parent_id',
        'is_default'
    ];

    public function media()
    {
        return $this->hasMany(Media::class, 'media_folder_id');
    }

    public function children()
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }
}
