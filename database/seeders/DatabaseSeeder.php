<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(UniverzitetSeeder::class);
        $this->call(FakultetSeeder::class);
        $this->call(NivoStudijaSeeder::class);
        $this->call(PredmetiSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(ProfesorSeeder::class);
        $this->call(MobilnostSeeder::class);
        $this->call(LearningAgreementSeeder::class);
        $this->call(PrepisSeeder::class);
        $this->call(PrepisAgreementSeeder::class);
    }
}
