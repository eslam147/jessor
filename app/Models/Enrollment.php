<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dates = ['expires_at'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function scopeActiveEnrollments($q)
    {
        return $q->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now()->toDateTimeString());
        })->where('user_id', auth()->user()->id)
            ->orderByDesc('id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
