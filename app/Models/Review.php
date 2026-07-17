<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'mal_id', 'rating', 'comment', 'is_spoiler'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
