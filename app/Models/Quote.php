<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['name','transport_packages_id','transport_id','rate','visa_id','profit_id','adult','children', 'infant','status'];

    // public function customers() {
    //     return $this->belongsTo(User::class, 'customer_id', 'id');
    // }
    public function transportpackages() {
        return $this->belongsTo(Transfer::class, 'transport_packages_id', 'id');
    }

    public function transports() {
        return $this->belongsTo(Transport::class, 'transport_id', 'id');
    }

    public function visas() {
        return $this->belongsTo(Visa::class, 'visa_id', 'id');
    }

    public function profits() {
        return $this->belongsTo(Profit::class, 'profit_id', 'id');
    }


    public function items() {
        return $this->hasMany(QuoteItem::class);
    }
}
