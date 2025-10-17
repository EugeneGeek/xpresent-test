<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Booking::insert([
            // Поездка на квадроцикле 30 минут
            ['service_id' => 1, 'client_name' => 'Иван', 'client_phone' => '+79990000001', 'date' => '2025-10-16', 'start_time' => '13:00:00', 'end_time' => '14:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 1, 'client_name' => 'Анна', 'client_phone' => '+79990000002', 'date' => '2025-10-16', 'start_time' => '16:00:00', 'end_time' => '17:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 1, 'client_name' => 'Сергей', 'client_phone' => '+79990000003', 'date' => '2025-10-17', 'start_time' => '10:00:00', 'end_time' => '11:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 1, 'client_name' => 'Ольга', 'client_phone' => '+79990000004', 'date' => '2025-10-17', 'start_time' => '11:00:00', 'end_time' => '12:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 1, 'client_name' => 'Дмитрий', 'client_phone' => '+79990000005', 'date' => '2025-10-17', 'start_time' => '13:00:00', 'end_time' => '14:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 1, 'client_name' => 'Юлия', 'client_phone' => '+79990000006', 'date' => '2025-10-17', 'start_time' => '18:00:00', 'end_time' => '19:00:00', 'created_at' => now(), 'updated_at' => now()],

            // Поездка на квадроцикле 60 минут
            ['service_id' => 2, 'client_name' => 'Максим', 'client_phone' => '+79990000007', 'date' => '2025-10-16', 'start_time' => '10:00:00', 'end_time' => '11:30:00', 'created_at' => now(), 'updated_at' => now()],

            // Тур на эндуро 60 минут
            ['service_id' => 3, 'client_name' => 'Илья', 'client_phone' => '+79990000008', 'date' => '2025-10-16', 'start_time' => '10:00:00', 'end_time' => '11:30:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 3, 'client_name' => 'Владимир', 'client_phone' => '+79990000009', 'date' => '2025-10-16', 'start_time' => '11:30:00', 'end_time' => '13:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['service_id' => 3, 'client_name' => 'Татьяна', 'client_phone' => '+79990000010', 'date' => '2025-10-16', 'start_time' => '18:30:00', 'end_time' => '20:00:00', 'created_at' => now(), 'updated_at' => now()],

            // Тур на эндуро 120 минут
            ['service_id' => 4, 'client_name' => 'Роман', 'client_phone' => '+79990000011', 'date' => '2025-10-17', 'start_time' => '14:00:00', 'end_time' => '16:30:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
