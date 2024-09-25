<?php

namespace App\Contracts;

use App\Dtos\VideoConference\VideoConferenceDTO;

interface VideoConferenceInterface
{
    public function setConfig(array $meetingDetails);
    public function createMeeting(array $meetingDetails): VideoConferenceDTO;
    public function getMeetingDetails(string $meetingId): VideoConferenceDTO;
    public function deleteMeeting(string $meetingId): bool;
    public function tutorial();
}
