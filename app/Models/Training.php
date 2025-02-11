<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Division;
use App\Models\Section;
use App\Models\User;
use App\Models\Trainer;
use App\Models\Institute;
use App\Models\Subject;
use App\Models\CostBreakDown;
use App\Models\Participant;
use App\Models\Remark;
use App\Models\Document;

class Training extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';  // Primary key is 'id' (custom format)
    public $incrementing = false;  // Disable auto-incrementing for the primary key
    protected $keyType = 'string'; // Ensure primary key is treated as a string

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
            // Generate a unique 'id' (primary key) in the format 'TR-001'
            $latest = self::orderBy('id', 'desc')->first();  // Get the latest ID in the table
            $number = $latest ? ((int) substr($latest->id, 3)) + 1 : 1; // Increment the number
            $training->id = 'TR-' . str_pad($number, 3, '0', STR_PAD_LEFT); // Format as TR-001, TR-002, etc.
        });
    }

    // Relationships
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainers()
    {
        return $this->belongsToMany(Trainer::class, 'training_trainers');
    }

    // In Training model
    public function institutes()
    {
        return $this->belongsToMany(Institute::class, 'training_institutes');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function costBrakedowns()
    {
        return $this->hasMany(Costbreak::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
