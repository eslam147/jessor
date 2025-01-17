<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSessions extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function student() {
        return $this->belongsTo(Students::class);
    }
}
