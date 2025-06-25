<?php

namespace App\Models\Testimonial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonials extends Model
{
    protected $fillable = ['image_url', 'name', 'company_name', 'designation', 'rating', 'feedback'];

}
