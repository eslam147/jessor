<?php

namespace App\Models;

use App\Models\ChatFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function modal()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->hasMany(ChatFile::class, 'message_id','id');
    }

    public function getFileUrlAttribute($value)
    {
        return tenant_asset($value);
    }
}
