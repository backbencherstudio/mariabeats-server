<?php

namespace App\Models\Service;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'image', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
