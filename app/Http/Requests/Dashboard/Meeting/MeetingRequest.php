<?php

namespace App\Http\Requests\Dashboard\Meeting;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MeetingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function onCreate()
    {
        $services = collect(config('meetings.providers', []))->pluck('name');
        return [
            "service" => ['required', Rule::in($services)],
            "password" => "nullable|string|min:6",
            "autorecord" => "nullable|boolean",
        ];
    }

    public function onUpdate()
    {
        return [
        ];
    }

    public function rules(): array
    {
        return match ($this->method()) {
            "POST" => $this->onCreate(),
            "PUT" => $this->onUpdate(),
        };
    }
}
