<?php

namespace App\Models\Featured;

use App\Models\Franchaisor\Franchaisor;
use Illuminate\Database\Eloquent\Model;

class Featured extends Model
{
    public function franchaisor()
    {
        return $this->belongsTo(Franchaisor::class);
    }
}
