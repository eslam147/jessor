<?php

namespace App\Dtos\VideoConference;

class VideoConferenceDTO
{
    public readonly ?string $meetingId;
    public readonly ?string $hostUrl;
    public readonly ?string $participantUrl;
    public readonly ?string $startTime;
    public readonly ?string $duration;
    public readonly ?string $timezone;

    public function __construct(string $meetingId, string $hostUrl, string $participantUrl, string $startTime, int $duration, string $timezone = null)
    {
        $this->meetingId = $meetingId;
        $this->hostUrl = $hostUrl;
        $this->participantUrl = $participantUrl;
        $this->startTime = $startTime;
        $this->duration = $duration;
    }

    public function toArray(): array
    {
        return [
            'meeting_id' => $this->meetingId,
            'host_url' => $this->hostUrl,
            'participant_url' => $this->participantUrl,
            'start_time' => $this->startTime,
            'duration' => $this->duration,
        ];
    }
}
