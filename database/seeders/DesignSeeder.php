<?php

namespace Database\Seeders;

use App\Models\Design;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Design::factory()
        ->count(100)
        ->create();
    }
}
