<?php

namespace App\Models;

use App\Models\MultipleEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function multipleEvent()
    {
        return $this->hasMany(MultipleEvent::class);

    }
    public function getImageAttribute($value)
    {
        if ($value) {
            return tenant_asset($value);
        }
        return '';
    }
}
