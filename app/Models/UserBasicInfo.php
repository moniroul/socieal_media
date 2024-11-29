<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBasicInfo extends Model
{
    protected $fillable = ['user_id', 'address', 'postcode', 'number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
