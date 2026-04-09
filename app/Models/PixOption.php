<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PixOption extends Model
{
    /** @use HasFactory<\Database\Factories\PixOptionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'photo', 'value', 'is_available'];

    protected $casts = [
        'value'        => 'float',
        'is_available' => 'boolean',
    ];

    public function contributions(): HasMany
    {
        return $this->hasMany(PixContribution::class);
    }
}
