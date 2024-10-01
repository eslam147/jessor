<?php
namespace App\Services\Meeting\Providers\Zoom;

use Jubaer\Zoom\Facades\Zoom;
use Illuminate\Support\Facades\Config;
use App\Contracts\MeetingProviderContract;
use App\Dtos\Meeting\MeetingInfoDto;
use App\Dtos\Meeting\MeetingResponseDTO;
use Jubaer\Zoom\Facades\Zoom as ZoomFacade;

class ZoomService implements MeetingProviderContract
{
    public function __construct(
        // private readonly ZoomService $zoomService
    ) {
    }
    public function getFacadeAccessor(): string
    {
        return 'zoom';
    }
    public function tutorial()
    {
        return view('meeting_provider.tutorials.inc.zoom');
    }

    public function getMeetingDetails(string $meetingId): MeetingResponseDTO
    {
        return $this->transform(ZoomFacade::getMeeting($meetingId));
    }
    public function deleteMeeting(string $meetingId): bool
    {
        return ZoomFacade::deleteMeeting($meetingId)['status'];
    }

    public function setConfig(array $data)
    {
        return Config::set([
            'zoom.client_id' => $data['client_id'],
            'zoom.client_secret' => $data['client_secret'],
            'zoom.account_id' => $data['account_id'],
        ]);
    }
    public function credentialsIsValid(): bool
    {
        return ! empty(Zoom::getAccessToken());
    }

    public function scheduling(MeetingInfoDto $content): MeetingResponseDTO
    {
        $zoomMeeting = ZoomFacade::createMeeting([
            "password" => $content->password,
            "topic" => $content->topic,
            "start_time" => $content->startTime,
            "timezone" => 'Africa/Cairo',
            "type" => 2,
            "settings" => [
                'join_before_host' => false, // if you want to join before host set true otherwise set false
                'host_video' => false, // if you want to start video when host join set true otherwise set false
                'participant_video' => false, // if you want to start video when participants join set true otherwise set false
                'mute_upon_entry' => false, // if you want to mute participants when they join the meeting set true otherwise set false
                'waiting_room' => false, // if you want to use waiting room for participants set true otherwise set false
                'audio' => 'both', // values are 'both', 'telephony', 'voip'. default is both.
                'auto_recording' => 'none', // values are 'none', 'local', 'cloud'. default is none.
                'approval_type' => 0, // 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required
            ],
            // "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            // "duration" => 60, // in minutes
            // "timezone" => 'Africa/Cairo', // set your timezone
            // "password" => 'set your password',
            // "start_time" => 'set your start time', // set your start time
            // "template_id" => 'set your template id', // set your template id  Ex: "Dv4YdINdTk+Z5RToadh5ug==" from https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingtemplates
            // "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
            // "schedule_for" => 'set your schedule for profile email ', // set your schedule for
            // "settings" => [
            //     'join_before_host' => false, // if you want to join before host set true otherwise set false
            //     'host_video' => false, // if you want to start video when host join set true otherwise set false
            //     'participant_video' => false, // if you want to start video when participants join set true otherwise set false
            //     'mute_upon_entry' => false, // if you want to mute participants when they join the meeting set true otherwise set false
            //     'waiting_room' => false, // if you want to use waiting room for participants set true otherwise set false
            //     'audio' => 'both', // values are 'both', 'telephony', 'voip'. default is both.
            //     'auto_recording' => 'none', // values are 'none', 'local', 'cloud'. default is none.
            //     'approval_type' => 0, // 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required
            // ],
        ]);
        return $this->transform($zoomMeeting['data']);
    }
    private function transform(array $meetingInfo)
    {
        return new MeetingResponseDTO(
            $meetingInfo['id'],
            $meetingInfo['start_url'],
            $meetingInfo['join_url'],
            $meetingInfo['start_time'],
            $meetingInfo['duration'],
            $meetingInfo['timezone']
        );
    }

    public function res($meetingId)
    {
        return ZoomFacade::rescheduleMeeting($meetingId, [
            "agenda" => 'your agenda',
            "topic" => 'your topic',
            "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            "duration" => 60, // in minutes
            "timezone" => 'Asia/Dhaka', // set your timezone
            "password" => 'set your password',
            "start_time" => 'set your start time', // set your start time
            "template_id" => 'set your template id', // set your template id  Ex: "Dv4YdINdTk+Z5RToadh5ug==" from https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingtemplates
            "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
            "schedule_for" => 'set your schedule for profile email ', // set your schedule for
            "settings" => [
                'join_before_host' => false, // if you want to join before host set true otherwise set false
                'host_video' => false, // if you want to start video when host join set true otherwise set false
                'participant_video' => false, // if you want to start video when participants join set true otherwise set false
                'mute_upon_entry' => false, // if you want to mute participants when they join the meeting set true otherwise set false
                'waiting_room' => false, // if you want to use waiting room for participants set true otherwise set false
                'audio' => 'both', // values are 'both', 'telephony', 'voip'. default is both.
                'auto_recording' => 'none', // values are 'none', 'local', 'cloud'. default is none.
                'approval_type' => 0, // 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required
            ],

        ]);
    }
}