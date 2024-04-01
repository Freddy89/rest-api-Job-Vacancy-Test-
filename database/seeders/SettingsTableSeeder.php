<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::truncate();

        Settings::create([
            'param' => 'create_job',
            'value' => 2
        ]);

        Settings::create([
            'param' => 'answer_job',
            'value' => 1
        ]);
    }
}
