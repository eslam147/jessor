<?php

namespace App\Http\Resources\Student\Subject;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Student\Lesson\LessonResource;

class SubjectResource extends JsonResource
{
    public $withoutLessons = false;
    public function withoutLessons()
    {
        $this->withoutLessons = true;
        return $this;
    }
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->when($this->image, tenant_asset($this->image), null),
            'lessons' => $this->unless($this->withoutLessons, LessonResource::collection($this->lessons), null),
        ];
    }
}
