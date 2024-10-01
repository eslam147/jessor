<?php
namespace App\Dtos\Meeting;

use Illuminate\Support\Carbon;

class MeetingInfoDto
{
    public readonly string $topic;
    public readonly Carbon $startTime;
    public readonly int $duration;
    public ?string $password = null;
    public array $metaAttributes = [];
    public function __construct(
        string $topic,
        Carbon $startTime,
        int $duration
    ) {
        $this->topic = $topic;
        $this->startTime = $startTime;
        $this->duration = $duration;
    }
    public function addExtraMetaAttribute(string $key, $value): self
    {
        $this->metaAttributes[$key] = $value;
        return $this;
    }
    public function __set($name, $value){
        $this->{$name} = $value;
    }
    public function toArray(): array
    {
        return [
            'topic' => $this->topic,
            'startTime' => $this->startTime->format('Y-m-d\TH:i:se'),
            'duration' => $this->duration,
            'metaAttributes' => $this->metaAttributes,
        ];
    }
}