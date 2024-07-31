<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonTopic extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($topic) { // before delete() method call this
            if ($topic->file) {
                foreach ($topic->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }
                $topic->file()->delete();
            }
        });
    }

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function file()
    {
        return $this->morphMany(File::class, 'modal');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function scopeRelatedToTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacherId = $user->teacher()->select('id')->pluck('id')->first();

            $query->whereHas('lesson', function ($q) use ($teacherId) {
                return $q->where('teacher_id', $teacherId);
            });

        }
        return $query;
    }

    public function scopeLessonTopicTeachers($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {

            $teacher_id = $user->teacher()->select('id')->pluck('id')->first();
            $subject_teacher = SubjectTeacher::select('class_section_id', 'subject_id')->where('teacher_id', $teacher_id)->get();
            
            if ($subject_teacher) {
                $subject_teacher = $subject_teacher->toArray();

                return $query->whereHas('lesson', function ($q) use ($subject_teacher) {
                    return $q
                        ->whereIn('class_section_id', array_column($subject_teacher, 'class_section_id'))
                        ->whereIn('subject_id', array_column($subject_teacher, 'subject_id'));
                });

            }
            return $query;
        }
        return $query;
    }
}
