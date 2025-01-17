<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnlineExamQuestionOption extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
}
