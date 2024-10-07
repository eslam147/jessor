<?php

namespace App\Models;

use App\Contracts\MeetingProviderContract;
use Illuminate\Support\Carbon;
use App\Enums\Lesson\LiveLessonStatus;
use App\Factories\MeetingProvider\MeetingProviderFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Meeting extends Model
{
    use SoftDeletes, HasFactory;
    protected $guarded = [];
    public $casts = [
        'meta' => 'json',
        'status' => LiveLessonStatus::class,
        'start_time' => 'datetime:Y-m-d\TH:i:se',
        'started_at' => 'datetime:Y-m-d\TH:i:se',
        'ended_at' => 'datetime:Y-m-d\TH:i:se',

    ];
    public function host()
    {
        return $this->morphTo();
    }
    public function scheduler()
    {
        return $this->morphTo();
    }
    public function start(): self
    {
        // $this->instance->starting($this);

        $startedAt = $this->started_at ?? now();
        $this->fill(['started_at' => $startedAt])->save();

        // $this->instance->started($this);

        return $this;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'scheduler_type',
        'scheduler_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'scheduler',

    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'end_time',
        'elapsed_time',
    ];
    public function getEndTimeAttribute(): Carbon
    {
        $startTime = clone $this->start_time;

        return $startTime->addMinutes($this->duration);
    }

    /**
     * Undocumented function
     *
     * @return int|null
     */
    public function getElapsedTimeAttribute(): ?int
    {
        if ($this->started_at) {
            $endedAt = $this->ended_at ?: now();

            return $this->started_at->diffInMinutes($endedAt);
        }

        return 0;
    }


    public function getInstanceAttribute(): MeetingProviderContract
    {
        return app(MeetingProviderFactory::class)->setProvider($this->provider)->build();
    }

    public function participants(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'participant', 'meeting_participants')
            ->using(MeetingParticipant::class)
            ->withPivot(['joined_at'])
            ->withTimestamps();
    }
}
