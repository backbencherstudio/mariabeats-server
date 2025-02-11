<?php

namespace App\Models\Address;

use App\Models\Franchaisor\FranchaisorCountries;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = ['name'];

    public function franchaisor()
    {
        return $this->hasMany(FranchaisorCountries::class, 'country_id');
    }
}
