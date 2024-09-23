<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';
    protected $files;
    const NAMESPACE_DIR_SEPARATOR = "\\"; 
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path('Services/' . $name . '.php');

        // 
        if ($this->files->exists($path)) {
            $this->error("Service $name already exists!");
            return;
        }

        // 
        $this->makeDirectory($path);

        // 
        $this->files->put($path, $this->getStub($name));
        $this->info("Service [$path] created successfully.");
    }
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }
    private function getNameSpace($fullPath)
    {
        $pathSplitted = explode('/', $fullPath);
        unset($pathSplitted[count($pathSplitted) - 1]);
        if ($this->files->exists($fullPath)) {
            throw new \Exception("$fullPath already exists!");
        }
        return join($this::NAMESPACE_DIR_SEPARATOR, ['App', 'Services', $pathSplitted]);
    }
    protected function getStub($fullNameSpace)
    {
        $name = explode('/', $fullNameSpace);
        $className = end($name);
        return "<?php
namespace {$this->getNameSpace($fullNameSpace)};

class $className
{
    // Your service logic here
}";
    }
}
