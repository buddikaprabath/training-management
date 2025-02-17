<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    // Define the fillable properties to protect against mass-assignment vulnerabilities
    protected $fillable = [
        'type',
        'amount',
        'provide_type',
        'division_id'
    ];

    public function getCreatedYearAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y');
    }
    // Define the relationship with the Division model
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
