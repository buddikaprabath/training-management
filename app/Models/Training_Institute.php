<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainingInstitute extends Pivot
{
    use HasFactory;

    protected $table = 'training_institutes'; // Explicitly defining the table name

    protected $fillable = [
        'training_id',
        'institute_id'
    ];
}
