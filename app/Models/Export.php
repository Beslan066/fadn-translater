<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = [
        'user_id', 'region_id', 'file_name', 'sentence_type', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
