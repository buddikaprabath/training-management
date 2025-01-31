<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainingTrainer extends Pivot
{
    use HasFactory;

    protected $table = 'training_trainers'; // Explicitly defining table name

    protected $fillable = [
        'training_id',
        'trainer_id'
    ];
}
