<?php

namespace App\Models;

use App\Observers\ProductReservationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ProductReservationObserver::class)]
class ProductReservation extends Model
{
    /** @use HasFactory<\Database\Factories\ProductReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'guest_name',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
