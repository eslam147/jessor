<?php

namespace App\Http\Resources\Student\LessonTopic;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Student\File\FileResource;
use App\Services\Purchase\PurchaseService;

class LessonTopicResource extends JsonResource
{
    public function toArray($request)
    {
        $userHasAccess = PurchaseService::userHasAccessToLesson($this->lesson_id, $request->user()->id);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'lesson_id' => $this->lesson_id,
            'files' => $this->when($userHasAccess, function () {
                return FileResource::collection($this->file);
            }, null),
        ];
    }
}
