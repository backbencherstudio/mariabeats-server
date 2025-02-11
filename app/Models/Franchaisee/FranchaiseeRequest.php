<?php

namespace App\Models\Franchaisee;

use Illuminate\Database\Eloquent\Model;

class FranchaiseeRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'country',
        'investment_amount',
        'timeframe',
        'preferred_location',
        'message',
        'status',
    ];
}
