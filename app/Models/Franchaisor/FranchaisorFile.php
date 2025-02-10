<?php

namespace App\Models\Franchaisor;

use Illuminate\Database\Eloquent\Model;

class FranchaisorFile extends Model
{
    protected $table = 'franchaisor_files';

    protected $fillable = ['franchaisor_id', 'file_path', 'file_name', 'file_type', 'type'];

    public function franchaisor()
    {
        return $this->belongsTo(Franchaisor::class, 'franchaisor_id');
    }
}
