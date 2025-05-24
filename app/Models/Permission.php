<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Models\PermissionGroup;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'permission_group_id',
        'name',
        'display_name',
        'guard_name',
    ];

    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }
}
