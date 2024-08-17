<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class DeviceHelper
{
    public static function getDeviceUniqueIdentifier()
    {
        // Generate a hash based on the combination of device attributes
        $deviceInfo = Str::uuid();

        // Create a unique hash
        $uniqueIdentifier = hash('sha256', $deviceInfo);

        return $uniqueIdentifier;
    }
}
