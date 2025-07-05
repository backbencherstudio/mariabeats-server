<?php

namespace App\Models\News;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['news_image', 'author', 'date', 'headline', 'concise_description', 'sub_headline', 'description'];
}
