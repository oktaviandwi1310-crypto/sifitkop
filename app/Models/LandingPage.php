<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'services' => 'array',
        'features' => 'array',
        'team' => 'array',
        'testimonials' => 'array',
    ];
}
