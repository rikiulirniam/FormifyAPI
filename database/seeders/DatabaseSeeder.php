<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::create([
            'name' => 'User 1',
            'email' => 'user1@webtech.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password1'),
            'remember_token' => Str::random(10),
        ]);
        User::create([
            'name' => 'User 2',
            'email' => 'user2@webtech.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password2'),
            'remember_token' => Str::random(10),

        ]);
        User::create([
            'name' => 'User 3',
            'email' => 'user3@worldskill.org',
            'email_verified_at' => now(),
            'password' => Hash::make('password3'),
            'remember_token' => Str::random(10),

        ]);
    }
}
