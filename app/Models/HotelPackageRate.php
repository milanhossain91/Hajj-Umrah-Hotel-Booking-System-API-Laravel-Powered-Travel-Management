<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelPackageRate extends Model
{
    use HasFactory;

    protected $table = 'hotel_packages_rates';

    protected $fillable = [
        'hotel_package_id',
        'room_type',
        'days_wd',
        'days_we'
    ];

    public function hotelpackage()
    {
        return $this->belongsTo(HotelPackage::class, 'hotel_package_id', 'id');
    }
}
