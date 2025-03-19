<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    // Since you're using a composite primary key, we define that here.
    protected $primaryKey = ['training_id', 'participant_id', 'subject_id'];
    public $incrementing = false; // Disable auto-incrementing for the primary key.

    protected $fillable = [
        'training_id',
        'participant_id',
        'subject_id',
        'grade'
    ];

    // Relationships
    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id', 'id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
