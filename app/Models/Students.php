<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Students extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $guarded = [];

    public function announcement()
    {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class.medium', 'section');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subjects()
    {
        $session_year_id = settingByType('session_year');
        // $session_year_id = $session_year;

        $classSection = optional($this->class_section);

        $class_section_id = $classSection->id;

        $core_subjects = (! empty($classSection->class->coreSubject)) ? $classSection->class->coreSubject->toArray() : [];

        $elective_subject_count = $classSection->class->electiveSubjectGroup->count();

        $elective_subjects = StudentSubject::where('student_id', $this->id)
            ->where('class_section_id', $class_section_id)
            ->where('session_year_id', $session_year_id)
            ->select("subject_id")->with('subject')
            ->get();

        $response = [
            'core_subject' => $core_subjects,
            'elective_subject' => ($elective_subject_count > 0 ? $elective_subjects : [])
        ];

        return $response;
    }

    public function classSubjects()
    {
        $core_subjects = $this->class_section->class->coreSubject;
        $elective_subjects = $this->class_section->class->electiveSubjectGroup->load('electiveSubjects.subject');
        return [
            'core_subject' => $core_subjects,
            'elective_subject_group' => $elective_subjects
        ];
    }

    //Getter Attributes
    public function getFatherImageAttribute($value)
    {
        if ($value) {
            return tenant_asset($value);
        }
        return null;
    }

    public function getMotherImageAttribute($value)
    {
        if ($value) {
            return tenant_asset($value);
        }
        return null;

    }

    public function father()
    {
        return $this->belongsTo(Parents::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(Parents::class, 'mother_id');
    }

    public function guardian()
    {
        return $this->belongsTo(Parents::class, 'guardian_id');
    }

    public function scopeOfTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            // for teacher list
            $class_teacher = $user->teacher->class_sections;
            $class_section_ids = array();
            if ($class_teacher->isNotEmpty()) {
                $class_section_ids = $class_teacher->pluck('class_section_id')->toArray();
            }
            $subject_teachers = $user->teacher->subjects;
            if ($subject_teachers) {
                foreach ($subject_teachers as $subject_teacher) {
                    $class_section_ids[] = array($subject_teacher->class_section_id);
                }
            }
            return $query->whereIn('class_section_id', $class_section_ids);
        } else {
            // for admin list
            return $query;
        }
        //return if doesn't affect above conditions
        return $query->where('class_section_id', 0);
    }

    public function exam_result()
    {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    public function exam_marks()
    {
        return $this->hasMany(ExamMarks::class, 'student_id');
    }

    public function fees_paid()
    {
        return $this->hasOne(FeesPaid::class, 'student_id')->with('class', 'session_year');
    }

    public function parents()
    {
        return $this->father()->union($this->mother())->union($this->guardian());
    }

    public function student_subjects()
    {
        return $this->hasMany(StudentSubject::class, 'student_id');
    }
}
