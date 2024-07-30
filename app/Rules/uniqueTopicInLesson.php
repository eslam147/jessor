<?php

namespace App\Rules;

use App\Models\Lesson;
use App\Models\LessonTopic;
use Illuminate\Contracts\Validation\Rule;

class uniqueTopicInLesson implements Rule
{

    public $lesson_id;
    public $topic_id;

    public function __construct($lesson_id, $topic_id = NULL)
    {
        $this->lesson_id = $lesson_id;
        $this->topic_id = $topic_id;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! empty($this->topic_id)) {
            $count = LessonTopic::where('name', $value)->relatedToTeacher()->where('lesson_id', $this->lesson_id)->count();
            if ($count == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $count = LessonTopic::where('name', $value)->relatedToTeacher()->where('lesson_id', $this->lesson_id)->whereNot('id', $this->topic_id)->count();
            if ($count == 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('topic_alredy_exists');
    }
}
