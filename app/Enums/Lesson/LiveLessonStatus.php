<?php
namespace App\Enums\Lesson;

use App\Traits\EnumToArray;

enum LiveLessonStatus: string
{
    use EnumToArray;
    case FINISHED = 'finished';
    case SCHEDULED = 'scheduled';
    case STARTED = 'started';
    case CANCELLED = 'cancelled';
    const DEFAULT = self::SCHEDULED->value; 

    public function color(){
        return match($this){
            self::FINISHED => 'success',
            self::CANCELLED => 'danger',
            self::SCHEDULED => 'info',
            self::STARTED => 'dark',
        };
    }

    public function translatedName(){
        return match($this){
            self::FINISHED => trans('sessions.finished'),
            self::SCHEDULED => trans('sessions.scheduled'),
            self::STARTED => trans('sessions.started'),
            self::CANCELLED => trans('sessions.cancelled'),
        };
    }
    public function isStarted(){
        return $this === self::STARTED;
    }
    public function isScheduled(){
        return $this === self::SCHEDULED;
    }
    public function isFinished(){
        return $this === self::FINISHED;
    }
}