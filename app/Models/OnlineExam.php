<?php

namespace App\Models;


use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class OnlineExam extends Model
{
    use HasFactory, SoftDeletes, HasRelationships;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $guarded = [];
    public $dates = [
        'start_date'
    ];

    protected $appends = ['total_mark','grade','highest_degree'];

    public function model()
    {
        return $this->morphTo();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function question_choice()
    {
        return $this->hasMany(OnlineExamQuestionChoice::class, 'online_exam_id');
    }

    public function student_attempt()
    {
        return $this->hasOne(StudentOnlineExamStatus::class, 'online_exam_id');
    }

    public function questions()
    {
        return $this->hasManyDeepFromRelations($this->question_choice(), (new OnlineExamQuestionChoice)->questions())->addSelect('online_exam_questions.*','online_exam_question_choices.marks as mark')->with('options');
    }
    public function answers()
    {
        return $this->hasManyDeepFromRelations($this->question_choice(), (new OnlineExamQuestionChoice)->questions(), (new OnlineExamQuestion)->answers());
    }

    public function student_answer()
    {
        return $this->hasManyThrough(OnlineExamStudentAnswer::class, OnlineExamQuestionChoice::class, 'online_exam_id', 'question_id')
        ->when(optional(auth()->user())->student, function ($query) {
            $query->where('online_exam_student_answers.student_id', auth()->user()->student->id);
        })
        ->addSelect('online_exam_student_answers.*', 'online_exam_question_choices.marks as mark', 'online_exam_question_choices.question_id as question_choice_id')
        ->with('options');
    }
    public function getTotalMarkAttribute()
    {
        return (int) $this->questions()->sum('online_exam_question_choices.marks');
    }

    public function getGradeAttribute()
    {
        if(auth()->check())
        {
            return (int) $this->student_answer()->sum('online_exam_question_choices.marks');
        }
        else
        {
            return 0;
        }
    }

    public function getHighestDegreeAttribute()
    {
        return ($this->grade / $this->total_mark)*100;
    }
}
