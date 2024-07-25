<?php

namespace Database\Seeders;

use App\Enums\Lesson\LessonStatus;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run()
    {
        $teachers = Teacher::with('subjectTeachers', 'classSections')->get();

        foreach ($teachers as $teacher) {
            if ($teacher->classSections->isNotEmpty() && $teacher->subjectTeachers->isNotEmpty()) {
                Lesson::create([
                    'teacher_id' => $teacher->id,
                    'name' => 'Lesson 1',
                    'description' => "Lesson 1 description",
                    'class_section_id' => $teacher->classSections->random()->id,
                    'subject_id' => $teacher->subjectTeachers->random()->subject_id,
                    'status' => LessonStatus::PUBLISHED
                ]);
            }
        }
    }
}
