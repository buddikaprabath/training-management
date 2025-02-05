<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Subset;

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
            // Generate a unique 'id' (primary key) in the format 'UI-001'
            $latest = self::latest('created_at')->first();
            $number = $latest ? ((int) substr($latest->id, 3)) + 1 : 1;
            $training->id = 'TR-' . str_pad($number, 3, '0', STR_PAD_LEFT);
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

    public function institutes()
    {
        return $this->belongsToMany(Institute::class, 'training_institutes');
    }

    public function subjects()
    {
        return $this->hasMany(Subset::class);
    }

    public function costBrakedowns()
    {
        return $this->hasMany(CostBreakDown::class);
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
