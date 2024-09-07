<?php

namespace App\Models;

use App\Traits\BelongsToTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnlineExamQuestion extends Model
{
    use HasFactory, BelongsToTeacher;
    const EQUATION_BASED_TYPE = 1;
    const IMAGE_BASED_TYPE = 2;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $guarded = [];
    public function class_subject() {
        return $this->belongsTo(ClassSubject::class,'class_subject_id');
    }
    public function options(){
        return $this->hasMany(OnlineExamQuestionOption::class,'question_id');
    }
    public function student_answer(){
        return $this->hasMany(OnlineExamStudentAnswer::class,'question_id')->with('options');
    }
    public function answers(){
        return $this->hasMany(OnlineExamQuestionAnswer::class,'question_id')->with('options');
    }
    public function getImageUrlAttribute($value) {
        if($value){
            return tenant_asset($value);
        }
        return null;
    }
}
