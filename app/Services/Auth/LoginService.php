<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Services\UserDeviceHistory\UserDeviceHistoryService;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginService
{
    public function __construct(
        private readonly UserDeviceHistoryService $userDeviceHistoryService
    ) {

    }
    public function handleDeviceLimit(User $user)
    {
        $maxDevices = settingByType('device_limit') ;

        if ($user->hasRole('Student')) {
            if (! is_null($maxDevices)) {
                $deviceCount = $user->devices()->whereNull('session_end_at')->count();

                if ($deviceCount >= $maxDevices) {
                    auth()->logout();
                    throw new HttpResponseException($this->failedResponse());
                } else {
                    $token = $this->userDeviceHistoryService->storeUserLoginHistory($user);
                    return [
                        'success' => true,
                        'token' => $token
                    ];
                }
            }
        }
        return [
            'success' => false,
            'token' => null
        ];
    }
    protected function failedResponse()
    {
        if (request()->wantsJson()) {
            return response()->json(['error' => trans("auth.device_limit")], 401);
        } else {
            return to_route('login.view')->withErrors([
                'error' => trans("auth.device_limit")
            ]);
        }
    }
}
