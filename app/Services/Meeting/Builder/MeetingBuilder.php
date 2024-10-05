<?php

namespace App\Services\Meeting\Builder;

use Carbon\Carbon;
use App\Models\Meeting;
use App\Dtos\Meeting\MeetingInfoDto;
use Illuminate\Database\Eloquent\Model;
use App\Dtos\Meeting\MeetingResponseDTO;
use App\Contracts\MeetingProviderContract;
use App\Factories\MeetingProvider\MeetingProviderFactory;

class MeetingBuilder
{

    /**
     * @var \Carbon\Carbon
     */
    public Carbon $startTime;

    /**
     * @var int
     */
    public int $duration;

    /**
     * @var MeetingProviderContract
     */
    public MeetingProviderContract $provider;
    /**
     * @var string
     */
    public string $topic;
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public Model $scheduler;

    // /**
    //  * @var \Nncodes\Meeting\Contracts\Host
    //  */
    // public Host $host;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public Model $presenter;

    /**
     * @var array
     */
    public array $metaAttributes = [];

    /**
     * Undocumented function
     *
     * @param string $topic
     * @return self
     */
    public function withTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param \Carbon\Carbon $startTime
     * @return self
     */
    public function startingAt(Carbon $startTime): self
    {
        if ($startTime->isPast()) {
            //@todo exception startTime cannot be less than now
        }

        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $minutes
     * @return self
     */
    public function during(int $minutes): self
    {
        $this->duration = $minutes;

        return $this;
    }


    /**
     * Undocumented function
     *
     * @param string|null $provider
     * @return self
     */
    public function useProvider(?string $provider = null): self
    {
        $this->provider = app(MeetingProviderFactory::class)->setProvider($provider)->build();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $metaAttributes
     * @return self
     */
    public function withMetaAttributes(array $metaAttributes): self
    {
        $this->metaAttributes = array_merge(
            $metaAttributes,
            $this->metaAttributes
        );

        return $this;
    }
    /**
     * Undocumented function
     *
     * @param Model $scheduler
     * @return self
     */
    public function scheduledBy(Model $scheduler): self
    {
        $this->scheduler = $scheduler;

        return $this;
    }



    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function save(): Meeting
    {
        $meetingRequestData = new MeetingInfoDto(
            $this->topic,
            $this->startTime,
            $this->duration
        );
        $scheduledMeeting = $this->provider->scheduling($meetingRequestData);

        $meeting = $this->saveMeeting($scheduledMeeting);

        return $meeting;
    }

    private function saveMeeting(MeetingResponseDTO $scheduledMeeting): Meeting
    {
        $meeting = new Meeting([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'topic' => $this->topic,
            'start_time' => $this->startTime,
            'meta' => ['duration' => $this->duration],
            'provider' => $this->provider->getFacadeAccessor(),
            // -------------------------------- \\
            'meeting_id' => $scheduledMeeting->meetingId,
            // -------------------------------- \\
            'start_url' => $scheduledMeeting->hostUrl,
            'join_url' => $scheduledMeeting->participantUrl,
            // -------------------------------- \\
            'timezone' => $scheduledMeeting?->timezone,
            // -------------------------------- \\
            // 'password' => $request->password,
        ]);

        $meeting->scheduler()->associate($this->scheduler);
        $meeting->host()->associate($this->scheduler);


        // foreach ($this->metaAttributes as $key => $value) {
        //     $meeting->setMeta($key)->value($value);
        // }

        $meeting->save();

        return $meeting;
    }
}