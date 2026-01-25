<?php

namespace Database\Seeders;

use App\Models\Mobilnost;
use App\Models\Student;
use App\Models\Fakultet;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MobilnostSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::take(12)->get();
        $hostFakulteti = Fakultet::whereNotIn('naziv', ['FIT'])->get();

        if ($students->isEmpty() || $hostFakulteti->isEmpty()) {
            return;
        }

        $startBase = Carbon::create(2024, 2, 1);

        foreach ($students as $index => $student) {
            $host = $hostFakulteti[$index % $hostFakulteti->count()];

            Mobilnost::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'fakultet_id' => $host->id,
                ],
                [
                    'datum_pocetka' => $startBase->copy()->addMonths($index * 2)->toDateString(),
                    'datum_kraja' => $startBase->copy()->addMonths(($index * 2) + 5)->toDateString(),
                ]
            );
        }
    }
}
