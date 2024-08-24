<?php

namespace App\Services\Purchase;

use App\Models\Lesson;
use App\Models\Enrollment;

class PurchaseService
{
    public function __construct(private Enrollment $model)
    {
    }

    public function find($purchaseID)
    {
        return $this->model->whereId($purchaseID)->first();
    }


    private function responseContent(string $msg, bool $status = false): array
    {
        return [
            'message' => $msg,
            'status' => $status
        ];
    }
    public function isLessonAlreadyEnrolled(Lesson $lesson, $userId)
    {
        return $this->model
            ->where('lesson_id', $lesson->id)
            ->where('user_id', $userId)
            ->exists();
    }

    public function enrollLesson(Lesson $lesson, $userId)
    {
        return Enrollment::firstOrCreate([
            'lesson_id' => $lesson->id,
            'user_id' => $userId
        ], [
            'expires_at' => now()->addDays($lesson->expiry_days),
        ]);

    }


}