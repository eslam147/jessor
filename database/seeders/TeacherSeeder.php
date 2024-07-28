<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\FormField;
use App\Models\ClassSection;
use App\Models\ClassTeacher;
use App\Models\SubjectTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();

        $user->image = "";

        $user->password = Hash::make('123456');

        $user->first_name = fake()->firstName();
        $user->last_name = fake()->lastName();
        $user->gender = fake()->randomElement(['Male', 'Female']);
        $user->current_address = fake()->address();
        $user->permanent_address = fake()->address();
        $user->email = "teacher@gmail.com";
        $user->mobile = fake()->phoneNumber();
        $user->dob = fake()->date();
        $user->save();


        $teacher = new Teacher();

        $teacher->user_id = $user->id;

        $teacher->save();
        $user->revokePermissionTo([
            'student-create',
            'student-list',
            'student-edit',
            'student-delete',
            'parents-create',
            'parents-list',
            'parents-edit'
        ]);
        $user->assignRole('Teacher');

        // $user->assignRole(2);
        SubjectTeacher::create([
            'subject_id' => Subject::inRandomOrder()->value('id'),
            'teacher_id' => $teacher->id
        ]);
        ClassTeacher::create([
            'class_section_id' => 3,
            'class_teacher_id' => $teacher->id
        ]);

    }
}
