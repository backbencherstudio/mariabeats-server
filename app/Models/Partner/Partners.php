<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;

class Partners extends Model
{
    protected $fillable = ['label', 'heading'];
    
    public function logos()
    {
        return $this->hasMany(PartnerLogo::class, 'partner_id');
    }
}
