<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;

    // Define fillable fields for mass assignment
    protected $fillable = ['division_name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function section()
    {
        return $this->hasMany(Section::class);
    }
}
