<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use App\Traits\WithoutTrashedRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSection extends Model
{
    use SoftDeletes, HasFactory, WithoutTrashedRelations;
    protected $guarded = [];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function classTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teachers', 'class_section_id', 'class_teacher_id');
    }

    public function class_teachers()
    {
        return $this->hasMany(ClassTeacher::class, 'class_section_id')->select('class_teacher_id');
    }

    // public function streams()
    // {
    //     return $this->belongsTo(Stream::class,'stream_id');
    // }

    public function announcement()
    {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function subject_teachers()
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function scopeClassTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            return $query->where('class_teacher_id', $teacher->id);
        }
        return $query;
    }

    public function scopeSubjectTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $class_section_ids = $user->teacher->subjects()->pluck('class_section_id');
            return $query->whereIn('id', $class_section_ids);
        }
        return $query;
    }
}
