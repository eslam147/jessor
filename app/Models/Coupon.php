<?php

namespace App\Models;

use App\Enums\Course\CouponTypeEnum;
use App\Enums\Course\CouponCourseEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'type' => CouponTypeEnum::class,
        'expiry_date' => 'datetime',
        'is_disabled' => 'boolean',
    ];
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }
    public function onlyAppliedTo()
    {
        return $this->morphTo();
    }
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class,'teacher_id');
    }
    
}
