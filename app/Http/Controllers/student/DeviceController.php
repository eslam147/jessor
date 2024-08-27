<?php

namespace App\Http\Controllers\student;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        // التحقق مما إذا كانت البصمة مسجلة مسبقًا
        $existingDevice = $user->devices()->where('device_fingerprint', $request->device_fingerprint)->first();
        if (!$existingDevice) {
            // التحقق من الحد الأقصى للأجهزة
            $maxDevices = 3;
            if ($user->devices()->count() >= $maxDevices) {
                return response()->json(['error' => 'لقد وصلت إلى الحد الأقصى للأجهزة المسموح بها.'], 403);
            }
            // تخزين الجهاز الجديد
            $user->devices()->create([
                'device_name' => $request->header('User-Agent'),
                'device_ip' => $request->ip(),
                'device_fingerprint' => 0000111122223333,//$request->device_fingerprint,
            ]);
        }

        return response()->json(['message' => 'Device stored successfully']);
    }

}
