<?php

namespace Database\Seeders;

use App\Models\NivoStudija;
use Illuminate\Database\Seeder;


class NivoStudijaSeeder extends Seeder
{
    public function run(): void
    {
        NivoStudija::create(['naziv' => 'Osnovne']);
        NivoStudija::create(['naziv' => 'Master']);
    }
}
