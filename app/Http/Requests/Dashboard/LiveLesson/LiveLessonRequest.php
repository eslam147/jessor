<?php

namespace App\Http\Requests\Dashboard\LiveLesson;

use App\Rules\uniqueLiveLessonName;
use Illuminate\Foundation\Http\FormRequest;

class LiveLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function onCreate()
    {
        return [
            'name' => ['required', 'max:300', new uniqueLiveLessonName($this->class_section_id, $this->subject_id)],
            'description' => 'required',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'session_date' => 'required|date_format:Y-m-d\TH:i',
            'session_duration' => 'required|numeric|min:1',
        ];
    }

    public function onUpdate()
    {
        return [
            'name' => ['required', 'max:300', new uniqueLiveLessonName($this->class_section_id, $this->subject_id)],
            'description' => 'required',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'session_date' => 'required|date_format:Y-m-d\TH:i',
            'session_duration' => 'required|numeric|min:1',
            'password' => 'required|string|min:5'
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
