<?php

namespace App\Models;

use App\Models\User;
use App\Models\Command;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Freelancer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'displayed_name',
        'description',
        'cv',
        'occupation',
        'category',
        'skills',
        'education',
        'certification',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'freelancer_id');
    }      
    public function command()
    {
        return $this->hasMany(Command::class);
    }
}
