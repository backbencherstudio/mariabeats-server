<?php

namespace App\Models\Category;

use App\Models\Service\Service;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
