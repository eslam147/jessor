<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

class Teacher extends Model
{
    use SoftDeletes, HasRelationships;
    protected $guarded = [];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function announcement()
    {
        return $this->morphMany(Announcement::class, 'modal');
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classSections()
    {
        return $this->belongsToMany(ClassSection::class, 'class_teachers', 'class_teacher_id', 'class_section_id');
    }

    public function class_sections()
    {
        return $this->hasMany(ClassTeacher::class, 'class_teacher_id')->select('class_section_id');
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class, 'class_teacher_id');
    }
    public function subjects()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id');
    }

    public function classes()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id')->groupBy('class_section_id');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'teacher_id');
    }
    public function lessonTopics()
    {
        return $this->hasManyThrough(LessonTopic::class, Lesson::class);
    }
    public function questions()
    {
        return $this->hasMany(OnlineExamQuestion::class, 'teacher_id');
    }
    public function students()
    {
        return $this->hasManyDeep(Students::class, [Lesson::class, Enrollment::class], [
            'teacher_id',
            'lesson_id',
            'user_id',
        ], [
            'id',
            'id',
            'user_id',
        ]);
    }
    //Getter Attributes
    public function getImageAttribute($value)
    {
        if (! empty($value)) {
            return tenant_asset($value);
        }
        return null;
    }

    public function scopeTeachers($query)
    {
        if (Auth::user()->hasRole('Teacher')) {
            return $query->where('user_id', Auth::user()->id);
        }
        return $query;
    }

}
