<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'description',
        'details',
        'image1',
        'image2',
        'image3',
        'video',
        'price',
        'offer_name',
        'category',
        'skills',
        'delevery',
        'tags',
        'freelancer_id',
    ];

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}
