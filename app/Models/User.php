<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'full_name',
        'dob',
        'gender',
        'address',
        'dependent',
        'phone',
        'cccd',
        'password',
        'department',
        'position',
        'avatar',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob' => 'date',
        'dependent' => 'integer',
    ];

    public function userRoles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'user_roles', 'user_id', 'user_type');
    }

    public function monthTaxes(): HasMany
    {
        return $this->hasMany(MonthTax::class, 'user_id');
    }

    public function yearTaxes(): HasMany
    {
        return $this->hasMany(YearTax::class, 'user_id');
    }

    public function hasRole($role): bool
    {
        return $this->userRoles()->where('user_type', $role)->exists();
    }
}
