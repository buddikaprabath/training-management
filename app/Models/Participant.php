<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'completion_status',
        'division_id',
        'training_id',
        'section_id'
    ];
    protected $casts = [
        'training_id' => 'string', // Ensure training_id is handled as a string
    ];

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
    public function remarks()
    {
        return $this->hasMany(Remark::class, 'participant_id', 'id');
    }
    public function sureties()
    {
        return $this->hasMany(Surety::class, 'participant_id', 'id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
