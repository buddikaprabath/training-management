<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costbreak extends Model
{
    use HasFactory;

    protected $table = 'costbreaks';

    protected $fillable = [
        'cost_type',
        'amount',
        'training_id',
    ];

    protected $casts = [
        'training_id' => 'string', // Ensure training_id is handled as a string
    ];

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id', 'training_id');
    }
}
