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
            ['name' => 'MENA'],
            ['name' => 'UAE'],
            ['name' => 'Asia'],
            ['name' => 'Europe'],
            ['name' => 'Worldwide'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
