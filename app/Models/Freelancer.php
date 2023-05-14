<?php

namespace App\Models;

use App\Models\User;
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
        'skills',
        'education',
        'certification',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
