<?php

namespace App\Http\Resources\Student\File;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /*
          Type Is Meaning
         1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link	
         */
        return [
            'title' => $this->title,
            'thumbnail' => $this->file_thumbnail,
            'type' => $this->type,
            'type_name' => $this->type_detail,
            'url' => $this->file_url,
            'extension' => $this->file_extension
        ];
    }
}
