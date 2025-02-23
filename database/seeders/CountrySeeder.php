<?php

namespace Database\Seeders;

use App\Models\Address\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'GCC'],
            ['name' => 'MENA'],
            ['name' => 'UAE'],
            ['name' => 'Asia'],
            ['name' => 'Europe'],
            ['name' => 'Worldwide'],
        ];

        foreach ($countries as $country) {
            // check if country already exists do not create again
            if (Country::where('name', $country['name'])->exists()) {
                continue;
            }
            Country::create($country);
        }
    }
}
