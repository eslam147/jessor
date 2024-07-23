<?php

namespace Database\Seeders;

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
        $school_name = Settings::where('type','school_name')->first();
        $school_phone = Settings::where('type','school_phone')->first();
        $school_email = Settings::where('type','school_email')->first();
        $school_name->update([
            'message' => 'jessor',
        ]);
        $school_phone->update([
            'message' => '01090250088',
        ]);
        $school_email->update([
            'message' => 'info@jesoor.online',
        ]);
        Settings::create([
            'type'      => "show_teachers",
            'message'   => 'allow'
        ]);

    }
}
