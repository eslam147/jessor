<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Imports\CouponImport;
use Illuminate\Console\Command;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\File;

class ImportListCoupons extends Command
{
    private readonly Tenant $tenant;
    private readonly CouponImport $couponImport;

    public function __construct(
        CouponImport $couponImport
    ) {
        $this->couponImport = $couponImport;
        parent::__construct();
    }

    protected $signature = 'import:coupons {--tenant=} {--file=} {--class=} {--subject=} {--teacher=} {--tags=*}';

    protected $description = 'Coupons import';

    public function handle()
    {
        $tenant = Tenant::find($this->option('tenant'));
        if (! $tenant?->exists) {
            $this->error('Tenant not found');
            return Command::FAILURE;
        }
        $this->couponImport->setData(
            tags: (array) $this->option('tags'),
            classId: $this->option('class'),
            subjectId: $this->option('subject'),
            teacherId: $this->option('teacher')
        );
        $this->setTenant($tenant);
        $file = 'app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'coupons' . DIRECTORY_SEPARATOR . $this->option('file') . '.xlsx';

        if (! File::exists(storage_path($file))) {
            $this->error('File not found: ' . $file);
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
}
