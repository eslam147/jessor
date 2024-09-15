<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Facades\Auth;

class Students extends Model
{
    use SoftDeletes, HasFactory;

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

        $classSection = optional($this->class_section);

        $class_section_id = $classSection->id;

        $core_subjects = (! empty($classSection->class->coreSubject)) ? $classSection->class->coreSubject->toArray() : [];

        $elective_subject_count = $classSection->class?->electiveSubjectGroup->count() ?? 0;

        $elective_subjects = StudentSubject::where('student_id', $this->id)
            ->where('class_section_id', $class_section_id)
            ->where('session_year_id', $session_year_id)
            ->select("subject_id")->with('subject')
            ->get();

        return [
            'core_subject' => $core_subjects,
            'elective_subject' => ($elective_subject_count > 0 ? $elective_subjects : [])
        ];
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
            $teacher = $user->teacher;
            $class_teacher = $teacher->class_sections;
            $class_section_ids = [];
            if ($class_teacher->isNotEmpty()) {
                $class_section_ids = $class_teacher->pluck('class_section_id')->toArray();
            }
            $subject_teachers = $teacher->subjects;
            if ($subject_teachers) {
                foreach ($subject_teachers as $subject_teacher) {
                    $class_section_ids[] = [$subject_teacher->class_section_id];
                }
            }
            $query->whereHas(
                'user.enrollmentLessons',
                fn($q) => $q->where('teacher_id', $teacher->id)
            );
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

    /**
     * Scope a query to also find records matching a given search term in multiple columns and relationships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance.
     * @param string $search The search term to match against.
     * @return \Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    public function scopeAdvancedSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $search = "%{$search}%";
            $query->where('user_id', 'LIKE', $search)
                ->orWhere('class_section_id', 'LIKE', $search)
                ->orWhere('category_id', 'LIKE', $search)
                ->orWhere('admission_no', 'LIKE', $search)
                ->orWhere('roll_number', 'LIKE', $search)
                ->orWhere('caste', 'LIKE', $search)
                ->orWhere('religion', 'LIKE', $search)
                ->orWhere('admission_date', 'LIKE', date('Y-m-d', strtotime($search)))
                ->orWhere('blood_group', 'LIKE', $search)
                ->orWhere('height', 'LIKE', $search)
                ->orWhere('weight', 'LIKE', $search)
                ->orWhere('is_new_admission', 'LIKE', $search)
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', $search)
                        ->orWhere('mobile', 'LIKE', $search)
                        ->orWhere('last_name', 'LIKE', $search)
                        ->orWhere('email', 'LIKE', $search)
                        ->orWhere('dob', 'LIKE', $search);
                })
                ->orWhereHas('father', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', $search)
                        ->orwhere('last_name', 'LIKE', $search)
                        ->orwhere('email', 'LIKE', $search)
                        ->orwhere('mobile', 'LIKE', $search)
                        ->orwhere('occupation', 'LIKE', $search)
                        ->orwhere('dob', 'LIKE', $search);
                })
                ->orWhereHas('mother', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', $search)
                        ->orwhere('last_name', 'LIKE', $search)
                        ->orwhere('email', 'LIKE', $search)
                        ->orwhere('mobile', 'LIKE', $search)
                        ->orwhere('occupation', 'LIKE', $search)
                        ->orwhere('dob', 'LIKE', $search);
                })
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'LIKE', $search);
                });
        });
    }
}
