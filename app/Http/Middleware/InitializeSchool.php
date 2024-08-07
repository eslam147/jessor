<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Facades\Tenancy;

class InitializeSchool
{
    public function handle(Request $request, Closure $next)
    {
        $tenancy = tenant();
        if ($tenancy) {
            $settings = Settings::get();
            if ($settings->count()) {
                $this->setSchool($settings);
            }
        }
        return $next($request);
    }
    private function setSchool($settings)
    {
        $providers = [
            'stripe' => ['stripe_publishable_key', 'stripe_status', 'stripe_secret_key', 'stripe_webhook_secret', 'stripe_webhook_url'],
            'paystack' => ['paystack_status', 'paystack_public_key', 'paystack_secret_key', 'paystack_webhook_url'],
            'razorpay' => ['razorpay_status', 'razorpay_public_key', 'razorpay_secret_key', 'razorpay_webhook_url']
        ];

        foreach ($providers as $provider => $keys) {
            $settingsArray = $settings->whereIn('type', $keys)->pluck('message', 'type')->toArray();

            if (! empty($settingsArray) && ! empty($settingsArray["{$provider}_status"])) {
                config([
                    "services.{$provider}.webhook_url" => $settingsArray["{$provider}_webhook_url"] ?? null,
                    "services.{$provider}.webhook_secret" => $settingsArray["{$provider}_webhook_secret"] ?? null,
                    "services.{$provider}.public_key" => $settingsArray["{$provider}_public_key"] ?? null,
                    "services.{$provider}.secret_key" => $settingsArray["{$provider}_secret_key"] ?? null,
                ]);
            }
        }
        // ------------------------------------------------------------------------------------------------------------ \\
        $appSettings = $settings->whereIn('type', ['school_name', 'time_zone'])->pluck('message', 'type')->toArray();
        Config::set('app.name', $appSettings['school_name']);
        Config::set('app.timezone', $appSettings['time_zone']);
        // ------------------------------------------------------------------------------------------------------------ \\
        $mailValues = ['mail_host', 'mail_port', 'mail_mailer', 'mail_username', 'mail_password', 'mail_encryption', 'mail_send_from'];
        $mailSettings = $settings->whereIn('type', $mailValues)->pluck('message', 'type')->toArray();
        if (! empty($mailSettings)) {
            // mail.mailers.smtp.host
            // config([
            //     'mail.mailers.smtp.mailer' => $mailSettings['mail_mailer'],
            //     'mail.mailers.smtp.host' => $mailSettings['mail_host'],
            //     'mail.mailers.smtp.port' => $mailSettings['mail_port'],
            //     'mail.mailers.smtp.username' => $mailSettings['mail_username'],
            //     'mail.mailers.smtp.password' => $mailSettings['mail_password'],
            //     'mail.mailers.smtp.encryption' => $mailSettings['mail_encryption'],
            //     'mail.from.address' => $mailSettings['mail_send_from'],
            // ]);
        }

    }
}
