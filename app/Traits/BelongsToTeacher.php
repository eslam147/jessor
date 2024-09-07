<?php
namespace App\Traits;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTeacher
{
    protected static function booted()
    {
        static::creating(function (Model $model) {
            if (auth()->check()) {
                $user = Auth::user();
                if ($user->hasRole('Teacher')) {
                    $model->teacher_id = auth()->user()->teacher()->value('id');
                }
            }
        });
    }

    public function scopeRelatedToTeacher(Builder $query)
    {
        if (auth()->user()->hasRole('Teacher')) {
            return $query->where('teacher_id', auth()->user()->teacher()->value('id'));
        }
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }
}