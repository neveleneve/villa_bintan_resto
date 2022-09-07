<?php

use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'Bo Kuhn',
                'email' => 'zieme.mckenzie@example.net',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'kontak' => '082918829182',
                'role' => '0',
                'remember_token' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Bo Ciak',
                'email' => 'bociak@example.net',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => '1',
                'kontak' => '082929187218',
                'remember_token' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
