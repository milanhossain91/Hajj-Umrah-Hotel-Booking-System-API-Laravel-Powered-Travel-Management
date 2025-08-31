<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','item_type','percentage_markup','fixed_markup'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
