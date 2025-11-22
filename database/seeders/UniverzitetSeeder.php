<?php

namespace Database\Seeders;

use App\Models\Univerzitet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UniverzitetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Univerzitet::create([
            'naziv' => 'Univerzitet Crna Gora',
            'email' => 'info@ucg.cg',
            'drzava' => 'Crna Gora',
            'grad' => 'www.ucg.cg',
        ]);

        Univerzitet::create([
            'naziv' => 'Univerzitet Mediteran Crna Gora',
            'email' => 'info@unimed.cg',
            'drzava' => 'Crna Gora',
            'grad' => 'www.unimediteran.cg',
        ]);
       
    }
}
