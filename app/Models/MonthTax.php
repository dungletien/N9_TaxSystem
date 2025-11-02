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

    // Composite primary key
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

    /**
     * Set the keys for a save update query.
     */
    protected function setKeysForSaveQuery($query)
    {
        if (is_array($this->primaryKey)) {
            foreach ($this->primaryKey as $key) {
                $query->where($key, '=', $this->getOriginal($key, $this->getAttribute($key)));
            }
            return $query;
        }
        return parent::setKeysForSaveQuery($query);
    }

    /**
     * Get the value of the model's primary key.
     */
    public function getKey()
    {
        if (is_array($this->primaryKey)) {
            $key = [];
            foreach ($this->primaryKey as $k) {
                $key[$k] = $this->getAttribute($k);
            }
            return $key;
        }
        return parent::getKey();
    }
}
