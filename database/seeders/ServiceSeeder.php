<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::insert([
            ['id' => 1, 'name' => 'Поездка на квадроцикле (30 минут)', 'duration' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Поездка на квадроцикле (60 минут)', 'duration' => 60, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Тур на эндуро (60 минут)', 'duration' => 60, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Тур на эндуро (120 минут)', 'duration' => 120, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
