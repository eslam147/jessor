<?php

namespace App\Models;

use App\Enums\Lesson\LessonStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Znck\Eloquent\Traits\BelongsToThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory, BelongsToThrough;
    protected $guarded = [];
    public $casts = [
        'status' => LessonStatus::class,
    ];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $appends = ['is_lesson_free'];
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($lesson) { // before delete() method call this
            if ($lesson->file) {
                foreach ($lesson->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }

                $lesson->file()->delete();
            }
            if ($lesson->topic) {
                $lesson->topic()->delete();
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', LessonStatus::PUBLISHED);
    }
    public function scopeRelatedToTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->load('teacher')->teacher->id;
            return $query->where('teacher_id', $teacher_id);

        }
        return $query;
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function getIsLessonFreeAttribute()
    {
        return (isset($this->is_paid) && $this->is_paid == 0);
    }
    public function isFree()
    {
        return (isset($this->is_paid) && $this->is_paid == 0);
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsToThrough(ClassSchool::class, ClassSection::class);
    }
    public function scopeRelatedToClass(Builder $q, $classId)
    {
        return $q->whereHas('class', fn($q) => $q->where('classes.id', $classId));
    }
    public function scopeRelatedToCurrentStudentClass(Builder $q, Students $student)
    {
        return $q->whereHas('class', fn($q) => $q->where('class_sections.id', $student->class_section_id));
    }

    public function file()
    {
        return $this->morphMany(File::class, 'modal');
    }
    public function getThumbnailAttribute($value){
        if(isset($value)){
            return tenant_asset($value);
        }
    }
    public function topic()
    {
        return $this->hasMany(LessonTopic::class);
    }

    public function scopeLessonTeachers($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->select('id')->pluck('id')->first();
            $subject_teacher = SubjectTeacher::select('class_section_id', 'subject_id')->where('teacher_id', $teacher_id)->get();
            if ($subject_teacher) {
                $subject_teacher = $subject_teacher->toArray();
                $class_section_id = array_column($subject_teacher, 'class_section_id');
                $subject_id = array_column($subject_teacher, 'subject_id');
                return $query->whereIn('class_section_id', $class_section_id)->whereIn('subject_id', $subject_id);
            }
            return $query;

        }
        return $query;
    }
}
