<?php

namespace App\Rules;

use App\Models\LiveLesson;
use Illuminate\Contracts\Validation\Rule;

class uniqueLiveLessonName implements Rule
{
    public $classSectionId;
    public $subjectId;
    public $lessonId = null;
    public function __construct($classSectionId, $subjectId, $lessonId = null)
    {
        $this->classSectionId = $classSectionId;
        $this->subjectId = $subjectId;
        $this->lessonId = $lessonId;
    }

    public function passes($attribute, $value): bool
    {
        $checkIsLessonExists = LiveLesson::where('name', $value)->where([
            'class_section_id' => $this->classSectionId,
            'subject_id' => $this->subjectId,
        ])->relatedToTeacher()->when(filled($this->lessonId), function ($query) {
            $query->where('id', '!=', $this->lessonId);
        })->exists();
        return ! $checkIsLessonExists;
    }

    public function message()
    {
        return trans('lesson_alredy_exists', [
            'lesson' => request('name')
        ]);
    }
}
