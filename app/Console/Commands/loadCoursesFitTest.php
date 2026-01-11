<?php

namespace App\Console\Commands;

use App\Services\SubjectImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class loadCoursesFitTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fit-subject:load {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test loading fit subjects from FIT';

    /**
     * Execute the console command.
     */
    public function handle(SubjectImportService $service)
    {
        $path = $this->argument('file');

        $data = $service->loadCoursesFit($path);

        $this->info("Loaded " . count($data) . " courses.");
        
        $this->table(
            ['Sifra', 'Naziv', 'Engleski', 'Sem.', 'ECTS'],
            array_map(function($c) {
                return [
                    $c['Sifra Predmeta'],
                    Str::limit($c['Naziv Predmeta'], 30),
                    Str::limit($c['Naziv Engleski'], 30),
                    $c['Semestar'],
                    $c['ECTS']
                ];
            }, array_slice($data, 0, 10))
        );
    }
}
