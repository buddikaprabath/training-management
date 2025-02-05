<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostBreakDown extends Model
{
    use HasFactory;

    protected $table = 'cost_break_downs'; // Explicitly defining table name
    protected $fillable = [
        'cost_type',
        'amount',
        'training_id'
    ];

    // Define relationship with Training model
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
