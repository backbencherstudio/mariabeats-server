<?php

namespace App\Models\Franchaisor;

use Illuminate\Database\Eloquent\Model;

class FranchaisorRequest extends Model
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
