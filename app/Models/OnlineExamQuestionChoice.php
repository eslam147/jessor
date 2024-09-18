<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Relations\MultiHasManyThrough;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class OnlineExamQuestionChoice extends Model
{
    use HasFactory, HasRelationships;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $guarded = [];

    public function online_exam() {
        return $this->belongsTo(OnlineExam::class,'online_exam_id');
    }
    public function questions() {
        return $this->belongsTo(OnlineExamQuestion::class,'question_id');
    }

}
