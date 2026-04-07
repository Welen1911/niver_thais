<?php

namespace App\Observers;

use App\Models\ProductReservation;

class ProductReservationObserver
{
    public function created(ProductReservation $reservation): void
    {
        $product  = $reservation->product()->with('reservations')->first();
        $reserved = $product->reservations->sum('quantity');

        if ($reserved >= $product->stock) {
            $product->update(['is_available' => false]);
        }
    }
}
