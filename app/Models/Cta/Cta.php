<?php

namespace App\Models\Cta;

use Illuminate\Database\Eloquent\Model;

class Cta extends Model
{
    protected $fillable = ['headline', 'description', 'button_text', 'button_link', 'bg_image', 'secondary_image'];

    
}
