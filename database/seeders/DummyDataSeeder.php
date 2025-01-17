<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mediums;
use App\Models\Parents;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Settings;
use App\Models\Students;
use App\Models\ClassSchool;
use App\Models\SessionYear;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $medium = [
            ['id' => 1, 'name' => 'British School'],
            ['id' => 2, 'name' => 'American School'],
            ['id' => 3, 'name' => 'French School'],
        ];
        Mediums::upsert($medium, ['id'], ['name']);

        $sections = [
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
            ['id' => 3, 'name' => 'C'],
        ];
        Section::upsert($sections, ['id'], ['name']);

        $classes = [
            ['id' => 1, 'name' => 'إولى ثانوي', 'medium_id' => 2],
            ['id' => 2, 'name' => 'ثانيه ثانوي', 'medium_id' => 2],
        ];
        ClassSchool::upsert($classes, ['id'], ['name', 'medium_id']);

        $class_sections = [
            ['id' => 1, 'class_id' => 1, 'section_id' => 1],
            ['id' => 2, 'class_id' => 1, 'section_id' => 2],
            ['id' => 3, 'class_id' => 2, 'section_id' => 1],
            ['id' => 4, 'class_id' => 2, 'section_id' => 2],
            ['id' => 5, 'class_id' => 2, 'section_id' => 3],
        ];
        ClassSection::upsert($class_sections, ['id'], ['class_id', 'section_id']);

        $subjects = [
            ['id' => 1, 'name' => 'Maths', 'code' => 'MA', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Practical'],
            ['id' => 2, 'name' => 'Science', 'code' => 'SC', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Practical'],
            ['id' => 3, 'name' => 'English', 'code' => 'EN', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Theory'],
            ['id' => 4, 'name' => 'Physics', 'code' => 'GJ', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Theory'],
            ['id' => 5, 'name' => 'Chimstry', 'code' => 'SN', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Theory'],
            ['id' => 6, 'name' => 'French', 'code' => 'HN', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Theory'],
            ['id' => 7, 'name' => 'Computer', 'code' => 'CMP', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Practical'],
            ['id' => 8, 'name' => 'PT', 'code' => 'PT', 'bg_color' => '#5031f7', 'medium_id' => 2, 'type' => 'Practical'],

        ];
        Subject::upsert($subjects, ['id'], ['name', 'code', 'bg_color', 'image', 'medium_id', 'type']);

        $session_years = [
            ['id' => 1, 'name' => '2022', 'default' => 1, 'start_date' => Carbon::create('2022', '06', '01'), 'end_date' => Carbon::create('2023', '04', '30')],
            ['id' => 2, 'name' => '2023', 'default' => 0, 'start_date' => Carbon::create('2023', '06', '01'), 'end_date' => Carbon::create('2024', '04', '30')],
            ['id' => 3, 'name' => '2024', 'default' => 0, 'start_date' => Carbon::create('2024', '06', '01'), 'end_date' => Carbon::create('2025', '04', '30')],
            ['id' => 4, 'name' => '2025', 'default' => 0, 'start_date' => Carbon::create('2025', '06', '01'), 'end_date' => Carbon::create('2026', '04', '30')],
        ];
        SessionYear::upsert($session_years, ['id'], ['name', 'default', 'start_date', 'end_date']);

        $session_year_settings = [
            ['id' => 9, 'type' => 'session_year', 'message' => 1],
        ];
        Settings::upsert($session_year_settings, ['id'], ['type', 'message']);

        $student_categories = [
            ['id' => 1, 'name' => 'SC', 'status' => 1],
            ['id' => 2, 'name' => 'ST', 'status' => 1],
            ['id' => 3, 'name' => 'OBC', 'status' => 1],
            ['id' => 4, 'name' => 'General', 'status' => 1],
        ];
        Category::upsert($student_categories, ['id'], ['name', 'status']);


        //Users
        $users = [
            [
                'id' => 2,
                'image' => 'parents/user.png',
                'password' => Hash::make('123456'),
                'first_name' => 'father',
                'last_name' => 'account',
                'email' => 'father@gmail.com',
                'mobile' => 1234567890,
                'gender' => 'Male',
                'current_address' => 'Cairo',
                'permanent_address' => 'Cairo'
            ],
            [
                'id' => 3,
                'image' => 'parents/user.png',
                'password' => Hash::make('123456'),
                'first_name' => 'Mother',
                'last_name' => 'Account',
                'email' => 'mother@gmail.com',
                'mobile' => 1234567890,
                'gender' => 'Female',
                'current_address' => 'cairo',
                'permanent_address' => 'cairo'
            ],
            [
                'id' => 4,
                'image' => 'students/user.png',
                'password' => Hash::make('123456'),
                'first_name' => 'Student',
                'last_name' => 'Account',
                'email' => 'student@gmail.com',
                'mobile' => 1234567890,
                'gender' => 'Male',
                'current_address' => 'cairo',
                'permanent_address' => 'cairo'
            ]
        ];

        $studentRole = Role::where('name', 'Student')->first();

        // Upsert users
        User::upsert($users, ['id'], ['image', 'password', 'first_name', 'last_name', 'email', 'mobile', 'current_address', 'permanent_address']);

        // Assign role to users
        foreach ($users as $user) {
            $userModel = User::find($user['id']);
            if ($userModel) {
                $userModel->assignRole($studentRole);
            }
        }


        //Parents
        $parent = [
            [
                'id' => 1,
                'user_id' => 2,
                'first_name' => 'Sachin',
                'last_name' => 'Tendulkar',
                'image' => 'parents/user.png',
                'occupation' => 'Cricketer',
                'email' => 'father@gmail.com',
                'mobile' => 1234567890,
                'gender' => 'Male',
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'first_name' => 'Anjali',
                'last_name' => 'Tendulkar',
                'image' => 'parents/user.png',
                'occupation' => 'Housewife',
                'email' => 'mother@gmail.com',
                'mobile' => 1234567890,
                'gender' => 'Female',

            ],
            [
                'id' => 2,
                'user_id' => 3,
                'first_name' => 'Ajit',
                'last_name' => 'Tendulkar',
                'image' => 'parents/user.png',
                'occupation' => 'Job',
                'email' => 'guardian@gmail.com',
                'mobile' => 1234567890,
                'gender' => 'Male',

            ]
        ];
        Parents::upsert($parent, ['id'], ['user_id', 'first_name', 'last_name', 'image', 'occupation', 'email', 'mobile', 'dob', 'gender']);
        $class_section = ClassSection::find(3);
        ClassSubject::create([
            'class_id' => $class_section->class_id,
            'subject_id' => Subject::inRandomOrder()->value('id'),
            'type' => 'Compulsory',
        ]);
        // $class_sections->subject()->sync([1, 2]);
        //Student
        $studentInfo = [
            'id' => 1,
            'user_id' => 4,
            'class_section_id' => $class_section->id,
            'category_id' => 1,
            'admission_no' => 12345667,
            'roll_number' => 1,
            'caste' => 'Hindu',
            'religion' => 'Hindu',
            'blood_group' => 'B+',
            'height' => '5.5',
            'weight' => '59',
            'father_id' => 1,
            'mother_id' => 2,
            'guardian_id' => 3,
            'admission_date' => Carbon::create('2024', '04', '01')
        ];
        $student = Students::firstOrCreate([
            'id' => 1,
            'user_id' => 4,
        ], $studentInfo);

        $student->load('user');
        $student->user->assignRole('Student');

    }
}
