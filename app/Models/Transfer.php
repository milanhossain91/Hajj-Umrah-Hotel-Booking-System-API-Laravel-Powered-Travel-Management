<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transport_packages';

    protected $fillable = [
        'from_location',
        'to_location',
        'transport_id',
        'rate',
        'period_from',
        'period_till',
        'vice_versa'
    ];

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location');
    }
}
