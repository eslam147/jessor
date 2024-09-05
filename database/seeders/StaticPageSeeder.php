<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;

class StaticPageSeeder extends Seeder
{
    public ?string $siteName = null;
    public ?string $siteUrl = null;
    public function __construct(FilesystemTenancyBootstrapper $filesystemTenancyBootstrapper)
    {
        if (tenancy()->initialized) {
            $settings = Settings::pluck('message', 'type')->toArray();
            $this->siteName = $settings['school_name'];
            $this->siteUrl = tenancy()->tenant->domains()->value('domain');
            $filesystemTenancyBootstrapper->revert();
        }
    }

    public function run()
    {
        Settings::updateOrCreate([
            'type' => "terms_condition",
        ], [
            'message' => $this->termsAndConditionsContent()
        ]);
        Settings::updateOrCreate([
            'type' => "privacy_policy",
        ], [
            'message' => $this->privacyPolicyContent()
        ]);
    }

    private function currentSiteName()
    {
        return $this->siteName ?? "Add You School Name";
    }

    private function currentSiteUrl()
    {
        return $this->siteUrl ?? "Add You School Url";
    }

    private function privacyPolicyContent(): string
    {
        return strtr(Storage::get("static_pages/privacy_policy.txt"), [
            "\n" => "<br/>",
            "\r" => "",
            ":websiteName" => $this->currentSiteName(),
            ":today" => today()->toDateString(),
        ]);
    }
    private function termsAndConditionsContent(): string
    {
        return strtr(Storage::get("static_pages/terms.txt"), [
            "\n" => "<br/>",
            "\r" => "",
            ":webSiteName" => $this->currentSiteName(),
            ":webSiteUrl" => $this->currentSiteUrl(),
            ":today" => today()->toDateString(),
        ]);
    }
}
