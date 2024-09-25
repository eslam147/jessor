<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Teacher;
use App\Imports\CouponImport;
use App\Models\ClassSchool;
use App\Models\Subject;
use Illuminate\Console\Command;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\File;

class UpdateExpiryListCoupons extends Command
{
    private readonly Tenant $tenant;
    private readonly CouponImport $couponImport;

    public function __construct(
        CouponImport $couponImport
    ) {
        $this->couponImport = $couponImport;
        parent::__construct();
    }

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
        $this->checkIsDataCorrect();
        $file = $this->filePath();

        if (! File::exists(storage_path($file))) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }
        $this->setDataToImport($this->options());
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
    private function setDataToImport($options)
    {
        return $this->couponImport->setData(
            tags: (array) $options['tags'],
            classId: $options['class'],
            subjectId: $options['subject'],
            teacherId: $options['teacher']
        );
    }
    private function endTenant()
    {
        if ($this->tenant && tenancy()->initialized) {
            Tenancy::end();
        }
    }
    private function checkIsDataCorrect()
    {
        $teacherId = $this->option('teacher');
        $classId = $this->option('class');
        $subjectId = $this->option('subject');
        if ($teacherId) {
            $teacher = Teacher::find($teacherId);
            if (! $teacher) {
                throw new \Exception("Teacher with ID $teacherId not found.");
            }
        }

        if ($classId) {
            $class = ClassSchool::find($classId);
            if (! $class) {
                throw new \Exception("Class with ID $classId not found.");
            }
        }

        if ($subjectId) {
            $subject = Subject::find($subjectId);
            if (! $subject) {
                throw new \Exception("Subject with ID $subjectId not found.");
            }
        }

    }
    private function filePath()
    {
        return 'app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'coupons' . DIRECTORY_SEPARATOR . $this->option('file') . '.xlsx';
    }
}
