<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Mediums extends Model
{
    use SoftDeletes,HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $guarded = [];
    
    public function class(): HasMany
    {
        return $this->hasMany(ClassSchool::class, 'medium_id');
    }
    public function classSections(): HasManyThrough
    {
        return $this->hasManyThrough(ClassSection::class, ClassSchool::class, 'medium_id','class_id');
    }
}
