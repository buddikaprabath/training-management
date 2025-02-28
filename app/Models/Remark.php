<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use HasFactory;

    protected $fillable = [
        'remark',
        'training_id',
        'participant_id'
    ];

    protected $casts = [
        'training_id' => 'string', // Ensure training_id is handled as a string
    ];

    // Define relationships
    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id', 'training_id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
