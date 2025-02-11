<?php

namespace App\Models\Franchaisor;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FranchaisorCountries extends Model
{

    protected $table = 'franchaisor_countries';

    protected $fillable = ['name', 'code'];

    public function franchaisor()
    {
        return $this->hasMany(User::class, 'country', 'code');
    }
}
