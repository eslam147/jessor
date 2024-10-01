<?php

namespace App\Models;

use App\Traits\BelongsToTeacher;
use App\Traits\SchedulesMeetings;
use App\Enums\Lesson\LiveLessonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiveLesson extends Model
{
    use HasFactory, BelongsToTeacher, SchedulesMeetings;
    protected $guarded = [];
    public $casts = [
        'status' => LiveLessonStatus::class
    ];
    public $dates = [
        'session_date'
    ];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section');
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
    public function meeting(): MorphOne
    {
        return $this->morphOne(Meeting::class, 'scheduler');
    }

    public function participants()
    {
        return $this->morphMany(MeetingParticipant::class, Meeting::class);
    }
}
