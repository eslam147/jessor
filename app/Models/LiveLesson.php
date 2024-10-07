<?php

namespace App\Models;

use App\Traits\BelongsToTeacher;
use App\Traits\SchedulesMeetings;
use App\Enums\Lesson\LiveLessonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\PaymentStatus\PaymentStatus;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiveLesson extends Model
{
    use HasFactory, BelongsToTeacher, SchedulesMeetings;
    protected $guarded = [];
    public $casts = [
        'status' => LiveLessonStatus::class,
        'payment_status' => PaymentStatus::class,
    ];
    public $dates = [
        'session_start_at'
    ];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $appends = ["left_time_as_percent"];

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
        return $this->hasManyThrough(
            MeetingParticipant::class,
            Meeting::class,
            'scheduler_id'
        )->where(
                'scheduler_type',
                array_search(static::class, Relation::morphMap()) ?: static::class
            );
    }
    public function getLeftTimeAsPercentAttribute()
    {
        $totalMinutes = $this->created_at->diffInMinutes($this->session_start_at);
        $elapsedMinutes = $totalMinutes - $this->session_start_at->diffInMinutes();
        $progressPercentage = ($elapsedMinutes / $totalMinutes) * 100;

        return round($progressPercentage, 2);
    }
}
