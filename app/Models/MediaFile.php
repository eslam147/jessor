<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaFile extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getFileUrlAttribute($value){
        return tenant_asset($value);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
