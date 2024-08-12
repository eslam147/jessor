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
        return [
            
            'title' => $this->file_name,
            'thumbnail' => $this->file_thumbnail,
            
            'type' => $this->type,
            
            'type_name' => $this->type_detail,
            
            'url' => $this->file_url,

            'download_link' => $this->download_link
        ];
    }
}
