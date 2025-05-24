<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'sort',
        'is_active',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_group_id');
    }
}