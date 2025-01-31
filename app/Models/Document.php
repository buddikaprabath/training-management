<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'name',
        'status',
        'date_of_submitting',
        'training_id',
        'participant_id'
    ];

    // Define relationships
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
