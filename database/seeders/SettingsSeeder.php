<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school_name = Settings::where('type', 'school_name')->first();
        $school_phone = Settings::where('type', 'school_phone')->first();
        $school_email = Settings::where('type', 'school_email')->first();
        $school_name->update([
            'message' => 'Infinity School',
        ]);
        $school_phone->update([
            'message' => '123456789',
        ]);
        $school_email->update([
            'message' => 'info@infinityschool.net',
        ]);
        Settings::create([
            'type' => "show_teachers",
            'message' => 'allow'
        ]);
        Settings::create([
            'type' => "custom_browser",
            'message' => 'disabled'
        ]);
        Settings::create([
            'type' => "device_limit",
            'message' => '5'
        ]);
        Settings::create([
            'type' => "browser_url",
            'message' => 'Https://sample-browser-link.com'
        ]);
        Language::create([
            'name' => 'العربيه',
            'code' => 'ar',
            'status' => 0,
            'is_rtl' => 1,
        ]);
        Language::create([
            'name' => 'الانجليزيه',
            'code' => 'en',
            'status' => 1,
            'is_rtl' => 0,
        ]);
    }
}
