<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

class SyriaCityAreaSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Damascus' => [
                ['name' => 'Old City', 'fee' => 3.00],
                ['name' => 'Bab Touma', 'fee' => 3.50],
                ['name' => 'Malki', 'fee' => 4.00],
                ['name' => 'Mezzeh', 'fee' => 4.50],
                ['name' => 'Kafar Souseh', 'fee' => 5.00],
                ['name' => 'Jaramana', 'fee' => 5.50],
                ['name' => 'Qudssaya', 'fee' => 6.00],
                ['name' => 'Dummar', 'fee' => 5.00],
                ['name' => 'Barzeh', 'fee' => 5.50],
                ['name' => 'Jobar', 'fee' => 6.00],
            ],
            'Aleppo' => [
                ['name' => 'Aziziyah', 'fee' => 5.00],
                ['name' => 'Jamiliyah', 'fee' => 5.50],
                ['name' => 'Salaheddine', 'fee' => 6.00],
                ['name' => 'Al-Hamdaniyah', 'fee' => 6.50],
                ['name' => 'Al-Furqan', 'fee' => 7.00],
                ['name' => 'New Aleppo', 'fee' => 7.50],
                ['name' => 'Hanano', 'fee' => 6.00],
                ['name' => 'Sheikh Maqsoud', 'fee' => 8.00],
            ],
            'Homs' => [
                ['name' => 'Inshaat', 'fee' => 5.00],
                ['name' => 'Al-Waer', 'fee' => 5.50],
                ['name' => 'Bab Dreib', 'fee' => 6.00],
                ['name' => 'Ghouta', 'fee' => 6.50],
                ['name' => 'Zakiyah', 'fee' => 7.00],
                ['name' => 'City Center', 'fee' => 4.50],
            ],
            'Latakia' => [
                ['name' => 'Sheikh Daher', 'fee' => 5.00],
                ['name' => 'Ziraa', 'fee' => 5.50],
                ['name' => 'Al-Souq', 'fee' => 6.00],
                ['name' => 'Salibeh', 'fee' => 6.50],
                ['name' => 'Mashrouaa', 'fee' => 7.00],
                ['name' => 'Al-Raml', 'fee' => 5.50],
            ],
            'Tartus' => [
                ['name' => 'City Center', 'fee' => 5.00],
                ['name' => 'Al-Marina', 'fee' => 5.50],
                ['name' => 'Banias Road', 'fee' => 6.00],
                ['name' => 'Al-Raml', 'fee' => 6.50],
                ['name' => 'Safita Highway', 'fee' => 7.50],
            ],
            'Hama' => [
                ['name' => 'City Center', 'fee' => 5.00],
                ['name' => 'Hader', 'fee' => 6.00],
                ['name' => 'Masyaf Road', 'fee' => 6.50],
                ['name' => 'Epinal', 'fee' => 7.00],
                ['name' => 'Jarajmeh', 'fee' => 5.50],
            ],
            'Deir ez-Zor' => [
                ['name' => 'Hamidiyah', 'fee' => 8.00],
                ['name' => 'Joura', 'fee' => 8.50],
                ['name' => 'Al-Qusour', 'fee' => 9.00],
                ['name' => 'Harabesh', 'fee' => 9.50],
            ],
            'Raqqa' => [
                ['name' => 'Tameen', 'fee' => 8.00],
                ['name' => 'Rumaylah', 'fee' => 8.50],
                ['name' => 'Al-Mashlab', 'fee' => 9.00],
                ['name' => 'City Center', 'fee' => 7.50],
            ],
            'Daraa' => [
                ['name' => 'City Center', 'fee' => 7.00],
                ['name' => 'Nawa', 'fee' => 8.00],
                ['name' => 'Tariq al-Sad', 'fee' => 7.50],
                ['name' => 'Daraa Balad', 'fee' => 8.50],
            ],
            'Idlib' => [
                ['name' => 'City Center', 'fee' => 8.00],
                ['name' => 'Ariha', 'fee' => 9.00],
                ['name' => 'Maarrat Misrin', 'fee' => 9.50],
                ['name' => 'Saraqib Road', 'fee' => 10.00],
            ],
            'Al-Hasakah' => [
                ['name' => 'Ghweiran', 'fee' => 9.00],
                ['name' => 'Nashwa', 'fee' => 9.50],
                ['name' => 'Al-Aziziyah', 'fee' => 10.00],
                ['name' => 'City Center', 'fee' => 8.50],
            ],
            'Qamishli' => [
                ['name' => 'Al-Wasta', 'fee' => 9.00],
                ['name' => 'Hilmiya', 'fee' => 9.50],
                ['name' => 'Al-Ras al-Ain Road', 'fee' => 10.50],
                ['name' => 'City Center', 'fee' => 8.50],
            ],
            'As-Suwayda' => [
                ['name' => 'City Center', 'fee' => 7.00],
                ['name' => 'Shahba', 'fee' => 8.00],
                ['name' => 'Salkhad Road', 'fee' => 8.50],
                ['name' => 'Al-Kafr', 'fee' => 9.00],
            ],
        ];

        foreach ($cities as $cityName => $areas) {
            $city = City::firstOrCreate(['name' => $cityName]);

            foreach ($areas as $area) {
                Area::firstOrCreate(
                    [
                        'city_id' => $city->id,
                        'name' => $area['name'],
                    ],
                    ['fee' => $area['fee']]
                );
            }
        }
    }
}
