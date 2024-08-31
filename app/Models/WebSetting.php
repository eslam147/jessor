<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebSetting extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getImageAttribute($value){
        if($value)
        {
            return tenant_asset($value);
        }
        return null;
    }
}
