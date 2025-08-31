<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id','hotel_packages_id','reservation_ref','date_from','date_to'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    // public function hotel()
    // {
    //     return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    // }

    public function hotelpackages()
    {
        return $this->belongsTo(HotelPackage::class, 'hotel_packages_id', 'id');
    }


    // public function customers()
    // {
    //     return $this->belongsTo(User::class, 'customer_id', 'id');
    // }

    // public function reservations()
    // {
    //     return $this->belongsTo(Reservation::class, 'reservations_id', 'id');
    // }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'item_id')->where('item_type', 'transfer');
    }

    public function visa()
    {
        return $this->belongsTo(Visa::class, 'item_id')->where('item_type', 'visa');
    }
}
