<?php
namespace App\Dtos\Meeting;

use Illuminate\Support\Carbon;

class ZoomMeetingInfoDto extends MeetingInfoDto
{
    public readonly string $topic;
    public readonly string $startTime;
    public readonly string $duration;
    public readonly string $timezone;
    public readonly ?string $password;
    public static string $metaSettingsKey = 'settings';

    public function __construct(
        string $topic,
        Carbon $startTime,
        int $duration,
        string $timezone,
        string $password
    ) {
        parent::__construct($topic, $startTime, $duration);
    }
    public function setSettings($key, $value): self
    {
        $this->addExtraMetaAttribute(self::$metaSettingsKey[$key], $value);
        return $this;
    }
    public function getSettings()
    {
        $this->metaAttributes[self::$metaSettingsKey] ?? [];
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['topic'],
            Carbon::parse($data['startTime']),
            $data['duration'],
            $data['timezone'],
            $data['password']
        );
    }

}