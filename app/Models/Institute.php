<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_institutes');
    }
}
