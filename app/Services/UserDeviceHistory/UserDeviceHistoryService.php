<?php
namespace App\Services\UserDeviceHistory;

use App\Models\UserDevice;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class UserDeviceHistoryService
{
    protected string $browser;
    protected string $deviceType;
    protected string $os;

    public function __construct(
        private readonly UserDevice $userDeviceModel
    ) {
        $agent = new Agent();

        $this->browser = $agent->browser();
        $this->deviceType = $agent->deviceType();

        $platform = $agent->platform();
        $version = $agent->version($platform);

        $this->os = "$platform-$version";
    }
    public function generateSessionToken()
    {
        return $this->browser . $this->deviceType . $this->os;
    }

    public function storeUserLoginHistory($user)
    {
        $ipAddress = request()->ip();
        $info = [
            'country' => null,
            'city' => null,
            'location' => null,
        ];
        $deviceToken = $this->generateSessionToken();
        $locationData = $this->getUserLocation($ipAddress);
        if (! empty($locationData) and ! empty($locationData['status']) and $locationData['status'] == "success") {
            $info['country'] = $locationData['country'] ?? null;
            $info['city'] = $locationData['city'] ?? null;
            $info['location'] = (! empty($locationData['lat']) and ! empty($locationData['lon'])) ? "{$locationData['lat']},{$locationData['lon']}" : null;
        } else {
            $ipAddress = null;
        }

        $userSession = session()->getId();

        $user->devices()->create([
            'browser' => $this->browser,
            'device' => $this->deviceType,
            'os' => $this->os,
            'ip' => $ipAddress,
            'device_ip' => request()->ip(),
            'country' => $info['country'],
            'city' => $info['city'],
            'location' => ! empty($info['location']) ? DB::raw('point(' . $info['location'] . ')') : null,
            'session_token' => $userSession,
            'session_start_at' => now(),
            'device_token' => $deviceToken
        ]);
        return encrypt($deviceToken);
    }
    public function checkSessionCookiesIsValid(): bool
    {
        $savedToken = null;
        try {
            $cookieToken = Cookie::get('device_token');
            if ($cookieToken) {
                $savedToken = decrypt($cookieToken);
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $savedToken = null;
        }

        if (! empty($savedToken) && $this->userDeviceModel->query()->where('device_token', $savedToken)->whereNull('session_end_at')->exists()) {
            $token = $this->generateSessionToken();
            return ($token === $savedToken);
        }
        return false;
    }
    public function storeUserLogoutHistory($userId)
    {
        if (! empty($userId)) {
            $session = $this->userDeviceModel->query()
                ->where('user_id', $userId)
                ->where('browser', $this->browser)
                ->where('device', $this->deviceType)
                ->where('os', $this->os)
                ->whereNull('session_end_at')
                ->orderByDesc('created_at')
                ->first();

            if (! empty($session)) {
                $session->update([
                    'session_end_at' => time(),
                    'end_session_type' => 'default'
                ]);

                $sessionManager = app('session');
                $sessionManager->getHandler()->destroy($session->session_id);
            }
        }
        Auth::logout();
    }
    private function getUserLocation($ipAddress)
    {
        $response = Http::get("http://ip-api.com/json/{$ipAddress}");
        $locationData = $response->json();

        return $locationData;
    }
}