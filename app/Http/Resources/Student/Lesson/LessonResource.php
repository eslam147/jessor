<?php

namespace App\Http\Resources\Student\Lesson;

use App\Http\Resources\Student\File\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Student\LessonTopic\LessonTopicResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'is_enrolled' => boolval($this->is_enrolled),
            'is_paid' => boolval($this->is_paid),
            'files' => $this->when($this->is_enrolled, function () {
                return FileResource::collection($this->file);
            }, null),
            // 'topics' => LessonTopicResource::collection($this->topics),
        ];
    }
}
