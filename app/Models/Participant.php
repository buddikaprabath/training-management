<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
            // Lock the table to ensure no other rows can be inserted while generating the ID
            DB::beginTransaction();

            try {
                // Get the latest participant and increment the ID
                $latest = self::orderBy('created_at', 'desc')->first();
                $number = $latest ? (int) substr($latest->id, 3) + 1 : 1;

                // Generate the new ID, ensuring it's unique
                $participant->id = 'PI-' . str_pad($number, 3, '0', STR_PAD_LEFT);

                // Commit the transaction after the ID is generated
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
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
    public function remarks()
    {
        return $this->hasMany(Remark::class, 'participant_id', 'id');
    }
    public function sureties()
    {
        return $this->hasMany(Surety::class, 'participant_id', 'id');
    }
}
