<?php

namespace Database\Seeders;

use App\Models\Home\HomeContents;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $video_data = [
            [
                'title' => 'Home Content Video 1',
                'heading' => 'Home Content Video 1 Heading',
                'description' => 'Home Content Video 1 Description',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'button_text' => 'Home Content Video 1 Button Text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        HomeContents::insert($video_data);
    }
}
