<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(){
        $this->call([
            InstallationSeeder::class,
            DummyDataSeeder::class,
            SmtpSeeders::class,
            SettingsSeeder::class,
            AddSuperAdminSeeder::class,
            AssignPermissionsToSuperAdminSeeder::class,
            TeacherSeeder::class,
            LessonSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
