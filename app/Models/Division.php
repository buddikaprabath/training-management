<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Section;
use App\Models\User;
use App\Models\Participant;


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

    public function budget()
    {
        return $this->hasMany(Budget::class);
    }

    public function training()
    {
        return $this->hasMany(Training::class);
    }

    public function participant()
    {
        return $this->hasMany(Participant::class);
    }
}
