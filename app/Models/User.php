<?php

namespace App\Models;

use Cog\Contracts\Ban\Bannable as BannableInterface;
use Cog\Laravel\Ban\Traits\Bannable;

use Bavix\Wallet\Traits\CanPay;
use Laravel\Sanctum\HasApiTokens;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\Customer;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Wallet, Customer, BannableInterface
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use SoftDeletes;
    use HasWallet, CanPay;
    use Bannable;
    protected $guarded = [];

    protected $appends =[
        'full_name'
    ];

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        "deleted_at",
        "created_at",
        "updated_at"
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function student()
    {
        return $this->hasOne(Students::class, 'user_id', 'id');
    }
    public function enrollmentLessons()
    {
        return $this->belongsToMany(Lesson::class, Enrollment::class, 'user_id', 'lesson_id');
    }

    public function parent()
    {
        return $this->hasOne(Parents::class, 'user_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id', 'id');
    }

    //Getter Attributes
    public function getImageAttribute($value)
    {
        return tenant_asset($value);
    }
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // //Getter Attributes
    // public function getOriginalImageAttribute() {
    //     return $this->getRawOriginal('image');
    // }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notifications');
    }

    public function messages()
    {
        return $this->morphMany(ChatMessage::class, 'modal');
    }
    public function couponUsages()
    {
        return $this->morphMany(CouponUsage::class, 'used_by_user');
    }
    public function hasAccessToLesson($lessonId)
    {
        return $this->whereHas('enrollmentLessons', function ($q) use ($lessonId) {
            return $q->where('lessons.id', $lessonId);
        })->exists();
    }
}
