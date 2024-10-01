<?php

namespace App\Models;

use App\Enums\Lesson\LiveLessonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $casts = [
        'meta' => 'json',
        'status' => LiveLessonStatus::class
    ];
    public function scheduler()
    {

        return $this->morphTo();
    }
}
