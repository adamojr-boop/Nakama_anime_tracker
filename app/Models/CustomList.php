<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomList extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'type', 'is_public', 'anime_ids'];

    protected $casts = [
        'anime_ids' => 'array',
        'is_public' => 'boolean',
    ];
}
