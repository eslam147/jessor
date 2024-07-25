<?php

namespace App\Http\Resources\Student\LessonTopic;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Student\File\FileResource;

class LessonTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'lesson_id' => $this->lesson_id,
            'files' => $this->when($request->user()->hasAccessToLesson($this->lesson_id), function () {
                return FileResource::collection($this->file);
            }, null),

        ];
    }
}
