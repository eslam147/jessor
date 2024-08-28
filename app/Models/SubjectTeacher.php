<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use App\Traits\WithoutTrashedRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectTeacher extends Model
{
    use SoftDeletes, WithoutTrashedRelations;
    protected $guarded = [];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class.medium', 'section');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->with('user');
    }

    public function scopeSubjectTeacher($query, $class_section_id = null)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            return $query->where('teacher_id', $user->teacher()->value('id'));
        }
        return $query;
    }
}