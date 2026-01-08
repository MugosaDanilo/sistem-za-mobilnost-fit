<?php

namespace App\Console\Commands;

use App\Services\SubjectImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class loadCoursesGenericTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generic-subject:load {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test loading generic subjects';

    /**
     * Execute the console command.
     */
    public function handle(SubjectImportService $service)
    {
        $path = $this->argument('file');

        $data = $service->loadCoursesGeneric($path);

        $this->info("Loaded " . count($data) . " courses.");
        
        $this->table(
            ['Sifra', 'Naziv', 'Engleski', 'Sem.', 'ECTS'],
            array_map(function($c) {
                return [
                    $c['Sifra Predmeta'],
                    Str::limit($c['Naziv Predmeta'], 30),
                    $c['Naziv Engleski'],
                    $c['Semestar'],
                    $c['ECTS']
                ];
            }, array_slice($data, 0, 10))
        );
    }
}
