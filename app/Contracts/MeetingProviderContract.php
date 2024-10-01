<?php

namespace App\Contracts;

use App\Dtos\Meeting\MeetingInfoDto;
use App\Dtos\Meeting\MeetingResponseDTO;

interface MeetingProviderContract
{
    public function credentialsIsValid(): bool;
    public function setConfig(array $meetingDetails);
    public function scheduling(MeetingInfoDto $meetingDetails): MeetingResponseDTO;
    public function getMeetingDetails(string $meetingId): MeetingResponseDTO;
    public function deleteMeeting(string $meetingId): bool;
    public function getFacadeAccessor(): string;
    public function tutorial();
}
