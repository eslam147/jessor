<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use App\Enums\Course\CouponTypeEnum;
use App\Enums\Course\CouponCourseEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory,HasTags, SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'type' => CouponTypeEnum::class,
        'expiry_date' => 'datetime',
        'is_disabled' => 'boolean',
    ];
    public $appends = ['left_days_count'];
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }
    public function onlyAppliedTo()
    {
        return $this->morphTo();
    }
    public function getLeftDaysCountAttribute(){
        return $this->expiry_date?->diffInDays(now());
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
    public function classModel()
    {
        return $this->belongsTo(ClassSchool::class, 'class_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
