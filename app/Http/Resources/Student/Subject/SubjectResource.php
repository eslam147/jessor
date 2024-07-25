<?php

namespace App\Http\Resources\Student\Subject;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Student\Lesson\LessonResource;

class SubjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->when($this->image, asset($this->image), null),
            'lessons' => LessonResource::collection($this->lessons),
        ];
    }
}
