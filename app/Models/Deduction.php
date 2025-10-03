<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $fillable = [
        'month',
        'year',
        'self_deduction',
        'dependent_deduction',
    ];

    protected $casts = [
        'self_deduction' => 'decimal:2',
        'dependent_deduction' => 'decimal:2',
    ];
}
