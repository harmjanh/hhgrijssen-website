<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Zaal achter kansel',
                'description' => '',
                'is_active' => true,
            ],
            [
                'name' => 'Consistorie',
                'description' => '',
                'is_active' => true,
            ],
            [
                'name' => 'Zaal onder gallerij',
                'description' => '',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::updateOrCreate(['name' => $roomData['name']], $roomData);
        }
    }
}
