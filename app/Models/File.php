<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;
    const FILE_UPLOAD_TYPE = 1;
    const VIDEO_UPLOAD_TYPE = 3;
    const VIDEO_CORNER_TYPE = 5;
    const YOUTUBE_TYPE = 2;
    const DOWNLOAD_LINK_TYPE = 6;
    protected $guarded = [];
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $appends = ['file_extension', 'type_detail'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($file) { // before delete() method call this
            if (Storage::disk('public')->exists($file->file_url)) {
                Storage::disk('public')->delete($file->file_url);
            }
        });
    }

    public function modal()
    {
        return $this->morphTo();
    }

    //Getter Attributes
    public function getFileUrlAttribute($value)
    {
        if ($this->type == 1 || $this->type == 3) {
            // IF type is File Upload or Video Upload then add Full URL.
            return url(Storage::url($value));
        }
        return $value;
    }

    //Getter Attributes
    public function getFileThumbnailAttribute($value)
    {
        if (! empty($value)) {
            return url(Storage::url($value));
        }
    }

    public function getFileExtensionAttribute()
    {
        if (! empty($this->file_url)) {
            return pathinfo(url(Storage::url($this->file_url)), PATHINFO_EXTENSION);
        }
        return "";
    }


    public function getTypeDetailAttribute()
    {
        //1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link
        if ($this->type == self::FILE_UPLOAD_TYPE) {
            return "File Upload";
        } elseif ($this->type == self::YOUTUBE_TYPE) {
            return "Youtube Link";
        } elseif ($this->type == self::VIDEO_UPLOAD_TYPE) {
            return "Video Upload";
        } elseif ($this->type == 4) {
            return "Other Link";
        } elseif ($this->type == self::VIDEO_CORNER_TYPE) {
            return "Video Corner Link";
        } elseif ($this->type == self::DOWNLOAD_LINK_TYPE) {
            return "Video Corner Download Link";
        }
    }
    public function isYoutubeVideo()
    {
        return $this->type == self::YOUTUBE_TYPE;
    }
    public function isVideoCorner()
    {
        return $this->type == self::VIDEO_CORNER_TYPE;
    }
    public function isVideoUpload()
    {
        return $this->type == self::VIDEO_UPLOAD_TYPE;
    }
}