<?php

namespace App\Services\Purchase;

use App\Models\Lesson;
use App\Models\Enrollment;

class PurchaseService
{
    public static $lessonEnrolled = [];
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

    public static function userHasAccessToLesson($lessonId, $userId): bool
    {
        if (! array_key_exists($lessonId, self::$lessonEnrolled)) {
            $enrollment = Enrollment::where('lesson_id', $lessonId)
                ->select('lesson_id', 'id')
                ->where('user_id', $userId)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->first();
            self::$lessonEnrolled[$lessonId] = ! empty($enrollment->id);
        }
        return array_key_exists($lessonId, self::$lessonEnrolled) && self::$lessonEnrolled[$lessonId];
    }

    public function isLessonAlreadyEnrolled(Lesson $lesson, $userId)
    {
        return $this->model
            ->where('lesson_id', $lesson->id)
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->exists();
    }


    public function enrollLesson(Lesson $lesson, $userId)
    {
        if ($this->isLessonAlreadyEnrolled($lesson, $userId)) {
            return false;
        }
        return Enrollment::create([
            'lesson_id' => $lesson->id,
            'user_id' => $userId,
            'expires_at' => empty($lesson->expiry_days) ? null : now()->addDays($lesson->expiry_days),
        ]);
    }


}