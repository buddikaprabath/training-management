<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Custom primary key
    public $incrementing = false; // Disable auto-incrementing for primary key
    protected $keyType = 'string'; // Primary key is a string

    protected $fillable = [
        'id',
        'name',
        'epf_number',
        'designation',
        'salary_scale',
        'location',
        'obligatory_period',
        'cost_per_head',
        'bond_completion_date',
        'bond_value',
        'date_of_signing',
        'age_as_at_commencement_date',
        'date_of_appointment',
        'date_of_appointment_to_the_present_post',
        'date_of_birth',
        'division_id',
        'training_id',
        'section_id'
    ];
    protected $casts = [
        'training_id' => 'string', // Ensure training_id is handled as a string
    ];

    protected static function booted()
    {
        static::creating(function ($participant) {
            // Generate custom 'id' (e.g., PI-001, PI-002, etc.)
            $latest = self::latest('created_at')->first();
            $number = $latest ? (int) substr($latest->id, 2) + 1 : 1;
            $participant->id = 'PI-' . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }

    // Relationships
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
