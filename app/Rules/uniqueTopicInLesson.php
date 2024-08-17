<?php

namespace App\Rules;

use App\Models\LessonTopic;
use Illuminate\Contracts\Validation\Rule;

class uniqueTopicInLesson implements Rule
{
    public $lesson_id;
    public $topic_id;
    public function __construct($lesson_id, $topic_id = null)
    {
        $this->lesson_id = $lesson_id;
        $this->topic_id = $topic_id;
    }

    public function passes($attribute, $value)
    {
        if (empty($this->topic_id)) {
            $count = LessonTopic::where('name', $value)->relatedToTeacher()->where('lesson_id', $this->lesson_id)->count();
            return boolval($count == 0);
        } else {
            $count = LessonTopic::where('name', $value)->relatedToTeacher()->where('lesson_id', $this->lesson_id)->whereNot('id', $this->topic_id)->count();
            return boolval($count == 0);
        }
    }

    public function message()
    {
        return trans('topic_alredy_exists');
    }
}
