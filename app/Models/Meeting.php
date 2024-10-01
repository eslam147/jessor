<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Enums\Lesson\LiveLessonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Meeting extends Model
{
    use HasFactory;
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
        $this->instance->starting($this);

        $startedAt = $this->started_at ?? now();
        $this->fill(['started_at' => $startedAt])->save();

        $this->instance->started($this);

        return $this;
    }
    use SoftDeletes;
    // use HasMetaAttributes;
    // use Traits\QueriesMeeting;
    // use Traits\DefinesMeetingRelationship;
    // use Traits\ManipulatesParticipants;
    // use Traits\ProvidesMeetingAccessors;
    // use Traits\ManipulatesMeeting;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'topic',
        'start_time',
        'duration',
        'started_at',
        'ended_at',
        'provider',
    ];

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
    /**
     * Get the MorphToMany Relation with the participant models
     *
     * @param string $modelType
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function participants(string $modelType): MorphToMany
    {
        return $this->morphedByMany($modelType, 'participant', 'meeting_participants')
            ->using(MeetingParticipant::class)
            ->withPivot(['uuid', 'started_at', 'ended_at'])
            ->withTimestamps();
    }
}
