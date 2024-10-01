<?php
namespace App\Factories\MeetingProvider;

use App\Contracts\MeetingProviderContract;
use App\Services\Meeting\Providers\Zoom\ZoomService;

class MeetingProviderFactory
{
    private static MeetingProviderContract $providerInstance;
    private static $credentials = [];
    public function setCredentials(array $credentials)
    {
        self::$credentials = $credentials;
        self::$providerInstance->setConfig($credentials);
        return $this;
    }
    public function getCredentials()
    {
        return self::$credentials;
    }
    public function setProvider($service)
    {
        switch ($service) {
            case 'zoom':
                self::$providerInstance = new ZoomService;
                return $this;
            default:
                throw new \Exception("Unsupported video conference service.");
        }
    }
    public function getProviderInstance()
    {
        return self::$providerInstance;
    }
    public function build()
    {
        switch (get_class(self::getProviderInstance())) {
            case ZoomService::class:
                $serviceSettings = $this->serviceSettings('zoom');

                // #TODO VALIDATE CREDENTIALS
                $this->setCredentials([
                    'client_id' => $serviceSettings['client_id'],
                    'client_secret' => $serviceSettings['client_secret'],
                    'account_id' => $serviceSettings['account_id'],
                ]);
                return $this->getProviderInstance();
        }
    }
    public function serviceSettings(string $serviceName): ?array
    {
        $settings = json_decode(settingByType("video_conference_" . strtolower($serviceName)), true) ?? [];
        return $settings;
    }
}
