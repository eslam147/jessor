<?php
namespace App\Traits;

use App\Models\Meeting;
use App\Services\Meeting\Builder\MeetingBuilder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait SchedulesMeetings
{
    public function meetings(): MorphMany
    {
        return $this->morphMany(Meeting::class, 'scheduler');
    }

    public function scheduleMeeting(?string $provider = null): MeetingBuilder
    {
        return app(MeetingBuilder::class)->useProvider($provider)->scheduledBy($this);
    }
}