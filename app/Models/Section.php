<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;
    protected $fillable = [
        'section_name',
        'division_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
