<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargoOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        "customer_id",
        "sender_details",
        "receiver_details",
        "cargo_origin_id",
        "cargo_destination_id",
        "weight",
        "item_description",
        "shipping_price",
        "dispatch_cost",
        "other_expenses",
        "total_amount",
        'total_expenses',
        'total_revenue',
        'payment_status',
        'partially_paid_amount',
        'remaining_amount',
        "payment_received",
        "dispatch_date",
        "shipping_status",
        "registered_by"
    ];

    protected $casts = [
        "other_expenses" => "array"
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function fromDestination()
    {
        return $this->belongsTo(Destination::class, 'cargo_origin_id');
    }

    public function toDestination()
    {
        return $this->belongsTo(Destination::class, 'cargo_destination_id');
    }
}


