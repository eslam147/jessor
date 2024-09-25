<?php
namespace App\Factories\VideoConference;

use App\Services\VideoConference\Zoom\ZoomService;

class VideoConferenceFactory
{
    public static $credentials = [];
    public static function setCredentials(array $credentials)
    {
        self::$credentials = $credentials;
    }
    public static function getCredentials()
    {
        return self::$credentials;
    }
    public static function make($service, $credentials = [])
    {
        switch ($service) {
            case 'zoom':
                $zoomService = new ZoomService;
                if (! empty($credentials)) {
                    self::setCredentials($credentials);
                } else {
                    self::setCredentials([
                        'client_id' => settingByType('zoom_client_id'),
                        'client_secret' => settingByType('zoom_client_secret'),
                        'account_id' => settingByType('zoom_account_id'),
                    ]);
                }
                $zoomService->setConfig(self::getCredentials());
                return $zoomService;
            default:
                throw new \Exception("Unsupported video conference service.");
        }
    }
}
