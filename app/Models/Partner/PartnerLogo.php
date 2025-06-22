<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;

class PartnerLogo extends Model
{
    protected $fillable = ['partner_id', 'logo'];
    
    public function partner()
    {
        return $this->belongsTo(Partners::class, 'partner_id');
    }
}
