<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Subject extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function medium()
    {
        return $this->belongsTo(Mediums::class);
    }
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }
    public function teachers()
    {
        return $this->hasManyThrough(Teacher::class, ClassSubject::class, 'subject_id', 'id');
    }

    public function onlineExams()
    {
        return $this->hasMany(OnlineExam::class, 'subject_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function scopeSubjectTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $subjects_ids = $user->teacher->subjects()->pluck('subject_id');
            return $query->whereIn('id', $subjects_ids);
        }
        return $query;
    }

    //Getter Attributes
    public function getImageAttribute($value)
    {
        if($value){
            return tenant_asset($value);
        }
        return global_asset('assets/images/subject.png');
    }
}
