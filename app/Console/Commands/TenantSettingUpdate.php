<?php

namespace App\Console\Commands;

use App\Models\Settings;
use Illuminate\Console\Command;

class TenantSettingUpdate extends Command
{
    protected $signature = 'tenant_settings:update --name={name} --address={address} --phone={phone} --timezone={timezone}';

    protected $description = 'Update Tenant Settings';

    public function handle()
    {
        $schoolName = $this->argument('name');
        $schoolAddress = $this->argument('address');
        $schoolPhone = $this->argument('phone');
        $schoolTimezone = $this->argument('timezone');

        $this->settingUpdate('name', $schoolName);
        $this->settingUpdate('address', $schoolAddress);
        $this->settingUpdate('phone', $schoolPhone);
        $this->settingUpdate('timezone', $schoolTimezone);

        return Command::SUCCESS;
    }
    private function settingUpdate($type, $message)
    {
        return Settings::updateOrCreate([
            'type' => $type
        ], [
            'message' => $message
        ]);
    }
}
