<?php

namespace App\Models;

use App\Enums\Lesson\LessonStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $casts = [
        'status' => LessonStatus::class,
    ];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

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

    public function scopeActive($query){
        return $query->where('status', LessonStatus::PUBLISHED);
    }
    public function scopeRelatedToTeacher($query){
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->load('teacher')->teacher->id;
            return $query->where('teacher_id', $teacher_id);
            
        }
        return $query;
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function isFree()
    {
        return (isset($this->is_paid) && $this->is_paid == 0);
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section');
    }

    public function file()
    {
        return $this->morphMany(File::class, 'modal');
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
