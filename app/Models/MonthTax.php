<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthTax extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'salary',
        'tax',
        'net_salary',
    ];

    protected $primaryKey = ['user_id', 'month', 'year'];
    public $incrementing = false;

    protected $casts = [
        'salary' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
