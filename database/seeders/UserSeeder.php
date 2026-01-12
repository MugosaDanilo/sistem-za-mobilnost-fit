<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $adminObj = new User();
        $adminObj->name = 'Admin';
        $adminObj->email = 'admin@gmail.com';
        $adminObj->password = Hash::make(env('ADMIN_PASSWORD', '12345')); // fallback '12345'
        $adminObj->type = 0;
        $adminObj->save();

        // Profesor
        $profesorObj = new User();
        $profesorObj->name = 'Profesor';
        $profesorObj->email = 'profesor@gmail.com';
        $profesorObj->password = Hash::make(env('PROFESOR_PASSWORD', '12345')); // opcionalni env
        $profesorObj->type = 1;
        $profesorObj->save();
    }
}
