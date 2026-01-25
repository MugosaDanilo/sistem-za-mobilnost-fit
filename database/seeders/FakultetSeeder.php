<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Database\Seeder;

class FakultetSeeder extends Seeder
{

    public function run(): void
    {
        $ucg = Univerzitet::firstOrCreate(
            ['naziv' => 'Univerzitet Crna Gora'],
            [
                'naziv' => 'Univerzitet Crna Gora',
            ]
        );

        $unimed = Univerzitet::firstOrCreate(
            ['naziv' => 'Univerzitet Mediteran Crna Gora'],
            [
                'naziv' => 'Univerzitet Mediteran Crna Gora',
            ]
        );

        $malardalen = Univerzitet::firstOrCreate(
            ['naziv' => 'Mälardalen University'],
            [
                'naziv' => 'Mälardalen University',
            ]
        );

        Fakultet::updateOrCreate(
            ['naziv' => 'ETF', 'univerzitet_id' => $ucg->id],
            [
                'email' => 'etf@ucg.cg',
                'telefon' => '033111222',
                'web' => 'etf.ucg.cg',
                'uputstvo_za_ocjene' => null,
            ]
        );

        Fakultet::updateOrCreate(
            ['naziv' => 'FIT', 'univerzitet_id' => $unimed->id],
            [
                'email' => 'fit@unimed.cg',
                'telefon' => '1111111',
                'web' => 'fit.unimed.cg',
                'uputstvo_za_ocjene' => null,
            ]
        );

        Fakultet::updateOrCreate(
            ['naziv' => 'School of Innovation, Design and Engineering (IDT)', 'univerzitet_id' => $malardalen->id],
            [
                'email' => 'idt-international@mdu.se',
                'telefon' => '+4621101300',
                'web' => 'https://www.mdu.se/en/malardalen-university/about-mdu/organisation/school-of-innovation-design-and-engineering',
                'uputstvo_za_ocjene' => null,
            ]
        );

        Fakultet::updateOrCreate(
            ['naziv' => 'Ekonomski fakultet', 'univerzitet_id' => $ucg->id],
            [
                'email' => 'ekonomski@ucg.cg',
                'telefon' => '020000111',
                'web' => 'www.ucg.cg',
                'uputstvo_za_ocjene' => null,
            ]
        );
    }
}
