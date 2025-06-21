<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Model;

class HomeContents extends Model
{
    protected $fillable = [
        'title',
        'sub_title',
        'heading',
        'description',
        'video_url',
        'image_url',
        'button_text',
        'button_link',
        'section_name',
        'file_url',
    ];
}
