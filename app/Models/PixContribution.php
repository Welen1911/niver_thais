<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PixContribution extends Model
{
    /** @use HasFactory<\Database\Factories\PixContributionFactory> */
    use HasFactory;

    protected $fillable = ['pix_option_id', 'guest_name', 'confirmed'];

    public function pixOption(): BelongsTo
    {
        return $this->belongsTo(PixOption::class);
    }
}
