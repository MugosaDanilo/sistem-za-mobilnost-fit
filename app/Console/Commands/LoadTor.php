<?php

namespace App\Console\Commands;

use App\Services\TorImportService;
use Illuminate\Console\Command;

class LoadTor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tor:load {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Tor file';

    /**
     * Execute the console command.
     */
    public function handle(TorImportService $service)
    {
        $file = $this->argument('file');

        $this->info("Loading file: {$file}");

        try {
            $courses = $service->import($file);
            
            $this->info("Loaded " . count($courses) . " courses.");

            $this->table(
                ['Term', 'Course', 'Grade', 'ECTS'],
                $courses
            );
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
