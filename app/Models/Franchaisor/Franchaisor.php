<?php

namespace App\Models\Franchaisor;

use App\Models\Address\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Franchaisor extends Model
{
    protected $table = 'franchaisors';

    protected $fillable = ['logo_path', 'brand_name', 'name', 'position', 'email', 'phone_number', 'address', 'industry', 'joined_at', 'end_at', 'brief_heading', 'brief_description', 'brief_country_of_region', 'brief_available', 'brief_business_type', 'brief_min_investment', 'details1_heading', 'details1_description', 'details2_heading', 'details2_description'];

    public function franchaisor()
    {
        return $this->belongsTo(User::class, 'franchaisor_id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function files()
    {
        return $this->hasMany(FranchaisorFile::class);
    }
}
