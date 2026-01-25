<?php

namespace Database\Seeders;

use App\Models\Univerzitet;
use Illuminate\Database\Seeder;

class UniverzitetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Univerzitet Crna Gora
        Univerzitet::updateOrCreate(
            ['naziv' => 'Univerzitet Crna Gora'],
            [
                'email' => 'info@ucg.cg',
                'drzava' => 'Crna Gora',
                'grad' => 'Podgorica',
            ]
        );

        // Univerzitet Mediteran Crna Gora
        Univerzitet::updateOrCreate(
            ['naziv' => 'Univerzitet Mediteran Crna Gora'],
            [
                'email' => 'info@unimed.cg',
                'drzava' => 'Crna Gora',
                'grad' => 'Podgorica',
            ]
        );

        // Mälardalen University – Švedska
        Univerzitet::updateOrCreate(
            ['naziv' => 'Mälardalen University'],
            [
                'email' => 'studenttorget@mdu.se',
                'drzava' => 'Švedska',
                'grad' => 'Västerås',
            ]
        );

        Univerzitet::updateOrCreate(
            ['naziv' => 'KTH Royal Institute of Technology'],
            [
                'email' => 'info@kth.se',
                'drzava' => 'Švedska',
                'grad' => 'Stockholm',
            ]
        );
    }
}
