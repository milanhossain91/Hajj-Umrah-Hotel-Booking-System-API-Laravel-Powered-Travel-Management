<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelPackage extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'hotel_id', 'res_no', 'city', 'item_type', 'extra_bed_rate', 'suppliment_haram', 'suppliment_kaaba', 'mealn_plan_bb',
        'mealn_plan_ld', 'period_from', 'period_till'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    public function hotelpackageitems()
    {
        return $this->hasMany(HotelPackageRate::class, 'hotel_package_id', 'id');
    }

}
