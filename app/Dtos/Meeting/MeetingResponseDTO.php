<?php

namespace App\Dtos\Meeting;

class MeetingResponseDTO
{
    public ?string $meetingId;
    public ?string $hostUrl;
    public ?string $participantUrl;
    public ?string $startTime;
    public ?string $duration;
    public ?string $timezone = null;

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
