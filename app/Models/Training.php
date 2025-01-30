<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';  // Primary key is 'id' (custom format)
    public $incrementing = false;  // Disable auto-incrementing for the primary key
    protected $fillable = [
        'id',
        'training_code',
        'training_name',
        'mode_of_delivery',
        'training_period_from',
        'training_period_to',
        'total_training_hours',
        'total_program_cost',
        'country',
        'training_structure',
        'exp_date',
        'batch_size',
        'awarding_institute',
        'training_custodian',
        'course_type',
        'category',
        'dead_line',
        'training_status',
        'feedback_form',
        'e_report',
        'warm_clothe_allowance',
        'presentation',
        'division_id',
        'section_id',
        'user_id'
    ];

    protected static function booted()
    {
        static::creating(function ($training) {
            // Generate custom 'id' (primary key) in the format 'UI-001', 'UI-002', etc.
            $latest = self::latest('created_at')->first();
            $number = $latest ? (int) substr($latest->id, 3) + 1 : 1;
            $training->id = 'UI-' . str_pad($number, 3, '0', STR_PAD_LEFT);

            // Generate custom 'training_code' in the format 'TC-001', 'TC-002', etc.
            $trainingCodeNumber = $latest ? (int) substr($latest->training_code, 3) + 1 : 1;
            $training->training_code = 'TC-' . str_pad($trainingCodeNumber, 3, '0', STR_PAD_LEFT);
        });
    }

    // Relations (example)
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
