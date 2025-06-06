<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
