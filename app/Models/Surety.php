<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surety extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'epf_number',
        'address',
        'mobile',
        'nic',
        'salary_scale',
        'designation',
        'participant_id'
    ];
    // Define relationships
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
