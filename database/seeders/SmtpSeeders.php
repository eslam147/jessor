<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmtpSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'type'    => 'mail_host',
            'message' => 'smtp.gmail.com',
        ]);
        Settings::create([
            'type'    => 'mail_mailer',
            'message' => 'smtp',
        ]);
        Settings::create([
            'type'    => 'mail_port',
            'message' => '587',
        ]);
        Settings::create([
            'type'    => 'mail_username',
            'message' => 'engelshafiy6@gmail.com',
        ]);
        Settings::create([
            'type'    => 'mail_password',
            'message' => 'andwvfrorxacchcv',
        ]);
        Settings::create([
            'type'    => 'mail_encryption',
            'message' => 'tls',
        ]);
        Settings::create([
            'type'    => 'mail_send_from',
            'message' => 'engelshafiy6@gmail.com',
        ]);
        Settings::create([
            'type'    => 'email_configration_verification',
            'message' => 1,
        ]);
    }
}
