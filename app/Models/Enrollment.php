<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dates = ['expires_at'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function scopeActiveEnrollments($q, $userId = null)
    {
        return $q->where(function ($q) {
            $q->where('expires_at', '>', now()->toDateTimeString())->orWhereNull('expires_at');
        })->where('user_id', $userId ?? auth()->user()->id)->orderByDesc('id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeTeacherFilter($q)
    {
        return $q->whereHas('lesson', fn($q) => $q->relatedToTeacher());
    }
}
