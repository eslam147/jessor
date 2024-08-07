<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationalProgram extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function getImageAttribute($value){
        return tenant_asset($value);
    }
}
