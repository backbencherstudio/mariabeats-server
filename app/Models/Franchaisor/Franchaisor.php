<?php

namespace App\Models\Franchaisor;

use App\Models\Address\Country;
use Illuminate\Database\Eloquent\Model;

class Franchaisor extends Model
{
    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function files()
    {
        return $this->hasMany(FranchaisorFile::class);
    }
}
