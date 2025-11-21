<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakultetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ucg = Univerzitet::where('naziv', 'Univerzitet Crna Gora')->first();
        $unimed = Univerzitet::where('naziv', 'Univerzitet Mediteran Crna Gora')->first();

        Fakultet::create([
            'naziv' => 'ETF',
            'email' => 'etf@ucg.cg',
            'telefon' => '033111222',
            'web' => 'etf.ucg.cg',
            'uputstvo_za_ocjene' => null,
            'univerzitet_id' => $ucg->id,
        ]);

        Fakultet::create([
            'naziv' => 'FIT',
            'email' => 'fit@unimed.cg',
            'telefon' => '1111111',
            'web' => 'fit.unimed.cg',
            'uputstvo_za_ocjene' => null,
            'univerzitet_id' => $unimed->id,
        ]);
    }
}
