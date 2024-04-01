<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();

        $faker = \Faker\Factory::create();

        // Let's make sure everyone has the same password and
        // let's hash it before the loop, or else our seeder
        // will be too slow.
        $password = Hash::make('testpass');

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => $password,
            'coins' => 10
        ]);

        User::create([
            'name' => 'Tester',
            'email' => 'test@test.com',
            'password' => $password,
            'coins' => 10
        ]);

        // And now let's generate a few dozen users for our app:
        for ($i = 0; $i < 2; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => $password,
                'coins' => $faker->numberBetween(0, 10)
            ]);
        }
    }
}
