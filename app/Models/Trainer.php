<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'institute_id'
    ];

    // Define relationship with Institute model
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_trainers');
    }
}
