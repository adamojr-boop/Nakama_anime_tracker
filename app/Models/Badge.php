<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'icon', 'category'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badge')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
