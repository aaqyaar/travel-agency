<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        "customer_id",
        "supplier_id",
        "from_destination_id",
        "to_destination_id",
        "trip_type",
        "ticket_type",
        "total_amount",
        "airline_cost",
        "other_expenses",
        "payment_method_id",
        "ticket_attachment",
        'passenger_name',
        'booking_reference_number',
        "sales_status",
        "revenue",
        "registered_by",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function fromDestination()
    {
        return $this->belongsTo(Destination::class, 'from_destination_id');
    }

    public function toDestination()
    {
        return $this->belongsTo(Destination::class, 'to_destination_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
