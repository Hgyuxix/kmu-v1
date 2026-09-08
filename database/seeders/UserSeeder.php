<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'fo@kecmagelangutara.test'], [
            'name' => 'Front Office',
            'email' => 'fo@kecmagelangutara.test',
            'password' => Hash::make('password'),
            'role' => 'fo',
        ]);

        User::updateOrCreate(['email' => 'pejabat@kecmagelangutara.test'], [
            'name' => 'Pejabat Kecamatan',
            'email' => 'pejabat@kecmagelangutara.test',
            'password' => Hash::make('password'),
            'role' => 'pejabat',
        ]);
    }
}
