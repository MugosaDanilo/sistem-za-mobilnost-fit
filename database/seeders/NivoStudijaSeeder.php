<?php

namespace Database\Seeders;

use App\Models\NivoStudija;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <- OBAVEZNO

class NivoStudijaSeeder extends Seeder
{
    public function run(): void
    {
        NivoStudija::create(['naziv' => 'Osnovne']);
        NivoStudija::create(['naziv' => 'Master']);
    }
}
