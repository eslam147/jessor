<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function usedByUser()
    {
        return $this->morphTo();
    }

    public function appliedTo()
    {
        return $this->morphTo();
    }
}
