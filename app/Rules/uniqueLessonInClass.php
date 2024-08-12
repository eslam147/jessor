<?php

namespace App\Rules;

use App\Models\Lesson;
use Illuminate\Contracts\Validation\Rule;

class uniqueLessonInClass implements Rule
{
    public $class_section_id;
    public $subject_id;
    public $lesson_id = null;
    public function __construct($class_section_id, $subject_id, $lesson_id = NULL)
    {
        $this->class_section_id = $class_section_id;
        $this->subject_id = $subject_id;
        $this->lesson_id = $lesson_id;
    }

    public function passes($attribute, $value): bool
    {
        $checkIsLessonExists = Lesson::where('name', $value)->where([
            'class_section_id' => $this->class_section_id,
            'subject_id' => $this->subject_id,
        ])->relatedToTeacher()->when(filled($this->lesson_id), function ($query) {
            $query->where('id', '!=', $this->lesson_id);
        })->exists();
        return ! $checkIsLessonExists ? true : false;
    }

    public function message()
    {
        return trans('lesson_alredy_exists',[
            'lesson' => request('name')
        ]);
    }
}
