<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TimetableCollection extends ResourceCollection
{
    public function toArray($request) {
        $response = [];
        foreach ($this->collection as $key => $row) {
            $response[$key] = array(
                "start_time" => $row['start_time'],
                "end_time" => $row['end_time'],
                "day" => $row['day'],
                "link_name" => $row['link_name'],
                "live_class_url" => $row['live_class_url'],
                "day_name" => $row['day_name'],
                "subject" => $row['subject_teacher']['subject'],
                "teacher_first_name" => $row['subject_teacher']['teacher']['user']['first_name'],
                "teacher_last_name" => $row['subject_teacher']['teacher']['user']['last_name'],
            );
        }
        return $response;
    }
}
