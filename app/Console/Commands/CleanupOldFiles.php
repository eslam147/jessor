<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanupOldFiles extends Command
{
    protected $signature = 'files:cleanup';

    protected $description = 'Command description';

    public function handle()
    {
        $tenants = $this->getAllTenants();
        
        $now = now();
        
        foreach ($tenants as $tenantId) {
            $this->info("Cleaning up files for tenant: {$tenantId}");
            $tenantFolderName = config('tenancy.filesystem.suffix_base') . $tenantId;
            $directory = storage_path("{$tenantFolderName}/app/public/temp/exports/coupons");
            if (File::exists($directory)) {
                $files = File::files($directory);
                foreach ($files as $file) {
                    $fileCreatedTime = Carbon::createFromTimestamp(File::lastModified($file));
                    if ($now->diffInHours($fileCreatedTime) > 12) {
                        File::delete($file);
                        $this->info("Deleted file: {$file->getRealPath()} for tenant: {$tenantId}");
                    }
                }
            }
        }

        return Command::SUCCESS;
    }

    private function getAllTenants()
    {
        return Tenant::pluck('id')->toArray();
    }

}
