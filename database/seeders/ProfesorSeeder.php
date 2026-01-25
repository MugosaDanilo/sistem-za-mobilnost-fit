<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Predmet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfesorSeeder extends Seeder
{
    public function run(): void
    {
        $profesori = [
            ['name' => 'Zana Knezevic', 'email' => 'zana.knezevic@unimediteran.me', 'password' => '12345'],
            ['name' => 'Maja Delibasic', 'email' => 'maja.delibasic@unimediteran.me', 'password' => '12345'],
            ['name' => 'Tijana Markovic', 'email' => 'tijana.vujicic@unimediteran.me', 'password' => '12345'],
            ['name' => 'Tamara Knezevic', 'email' => 'tamara.knezevic@unimediteran.me', 'password' => '12345'],
            ['name' => 'Ivan Knezevic', 'email' => 'ivan.knezevic@unimediteran.me', 'password' => '12345'],
            ['name' => 'Nikola Cmiljanic', 'email' => 'nikola.cmiljanic@unimediteran.me', 'password' => '12345'],
        ];

        $predmeti = Predmet::all();
        if ($predmeti->isEmpty()) {
            return;
        }

        foreach ($profesori as $i => $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'name' => $p['name'],
                    'password' => Hash::make($p['password']),
                    'type' => 1,
                ]
            );

            $assign = $predmeti->slice($i * 2, 3)->pluck('id')->toArray();
            if (empty($assign)) {
                $assign = $predmeti->take(2)->pluck('id')->toArray();
            }

            $user->predmeti()->syncWithoutDetaching($assign);
        }
    }
}
