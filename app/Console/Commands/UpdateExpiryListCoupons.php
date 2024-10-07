<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use App\Imports\UpdateCouponImport;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\File;

class UpdateExpiryListCoupons extends Command
{
    private readonly Tenant $tenant;
    private readonly UpdateCouponImport $couponImport;

    public function __construct(
        UpdateCouponImport $couponImport
    ) {
        $this->couponImport = $couponImport;
        parent::__construct();
    }
    /*
    
    php artisan renewexpiry:coupons --tenant=zaakr --file=coupons_mina
    */
    protected $signature = 'renewexpiry:coupons {--tenant=} {--file=}';

    protected $description = 'Coupons Update Duration Import';

    public function handle()
    {
        $tenant = Tenant::find($this->option('tenant'));
        if (! $tenant?->exists) {
            $this->error('Tenant not found');
            return Command::FAILURE;
        }


        $this->setTenant($tenant);

        $file = $this->filePath();

        if (! File::exists(storage_path($file))) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }

        $this->importCoupons(storage_path($file));
        $this->endTenant();
        $this->info('Coupons imported successfully');
        return Command::SUCCESS;
    }
    private function importCoupons($fullFilePath)
    {
        $this->info("Importing coupons from: {$fullFilePath}");
        return ($this->couponImport)->withOutput($this->output)->import($fullFilePath);
    }
    private function setTenant($tenant)
    {
        $this->tenant = $tenant;
        Tenancy::initialize($tenant);
        $this->info("Tenant set: {$tenant->id}");
    }
    
    private function endTenant()
    {
        if ($this->tenant && tenancy()->initialized) {
            Tenancy::end();
        }
    }
    private function filePath()
    {
        return 'app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'coupons' . DIRECTORY_SEPARATOR . $this->option('file') . '.xlsx';
    }
}
