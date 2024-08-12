<?php

namespace App\Http\Resources\Student\File;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            
            'title' => $this->file_name,
            'thumbnail' => $this->when(!empty($this->file_thumbnail) , $this->file_thumbnail, global_asset('images/no_image_available.jpg')),
            
            'type' => $this->type,
            
            'type_name' => $this->type_detail,
            
            'url' => $this->file_url,

            'download_link' => $this->download_link
        ];
    }
}
