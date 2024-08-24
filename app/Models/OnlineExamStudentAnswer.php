<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class OnlineExamStudentAnswer extends Model
{
    use HasFactory, BelongsToThrough;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $guarded = [];

    public function online_exam()
    {
        return $this->belongsTo(OnlineExam::class, 'online_exam_id');
    }
    public function questionChoice()
    {
        return $this->belongsTo(OnlineExamQuestionChoice::class, 'question_id');
    }
    public function question()
    {
        return $this->belongsToThrough(
            OnlineExamQuestion::class,
            OnlineExamQuestionChoice::class,
            localKey: 'question_id',
            foreignKeyLookup: [
                OnlineExamQuestion::class => 'question_id',
                OnlineExamQuestionChoice::class => 'question_id'
            ]
        );
    }
}
