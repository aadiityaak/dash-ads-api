<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //default admin users
        $user = User::create([
            'name'              => 'admin',
            'email'             => 'admin@example.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ]);
        $user->assignRole('admin');

        //default owner users
        $owner = User::create([
            'name'              => 'owner',
            'email'             => 'owner@example.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ]);
        $owner->assignRole('owner');

        //default users
        $user = User::create([
            'name'              => 'user',
            'email'             => 'user@example.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ]);
        $user->assignRole('user');
    }
}
