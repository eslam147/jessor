<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Settings;
use Illuminate\Http\Request;
use Stancl\Tenancy\Facades\Tenancy;

class InitializeSchool
{
    public function handle(Request $request, Closure $next)
    {
        $tenancy = tenant();
        if ($tenancy) {
            // $settings = Settings::select('key', 'message')->get();
            // if ($settings->count()) {
            //     $this->setSchool($settings);
            // }
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
            $settingsArray = $settings->whereIn('key', $keys)->pluck('message', 'key')->toArray();

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
        $appSettings = $settings->whereBetween('key', ['school_name'])->pluck('message', 'key')->toArray();
        if (! empty($appSettings)) {
            config([
                'app.name' => $appSettings['school_name'],
                'app.timezone' => $appSettings['time_zone'],
            ]);
        }
        // ------------------------------------------------------------------------------------------------------------ \\
        $mailValues = ['mail_host', 'mail_port', 'mail_mailer', 'mail_username', 'mail_password', 'mail_encryption', 'mail_send_from'];
        $mailSettings = $settings->whereBetween('key', $mailValues)->pluck('message', 'key')->toArray();
        if (! empty($mailSettings)) {
            config([
                'mail.mailer' => $mailSettings['mail_mailer'],
                'mail.host' => $mailSettings['mail_host'],
                'mail.port' => $mailSettings['mail_port'],
                'mail.username' => $mailSettings['mail_username'],
                'mail.password' => $mailSettings['mail_password'],
                'mail.encryption' => $mailSettings['mail_encryption'],
                'mail.from.address' => $mailSettings['mail_send_from'],
            ]);
        }

    }
}
