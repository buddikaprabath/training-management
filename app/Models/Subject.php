<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_type',
        'subject_name',
        'training_id'
    ];

    // Define relationship with Training model
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
