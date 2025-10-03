<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YearTax extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'total_salary',
        'total_tax',
        'net_salary',
    ];

    protected $primaryKey = ['user_id', 'year'];
    public $incrementing = false;

    protected $casts = [
        'total_salary' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
