<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRate extends Model
{

    protected $fillable = [
        'transfer_id',
        'from_location',
        'to_location',
        'rate',
        'currency',
        'valid_until',
    ];
    public function transfer() {
        return $this->belongsTo(Transfer::class);
    }
}
