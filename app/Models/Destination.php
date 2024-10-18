<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "continent",
        "iso_country",
        "municipality",
        "registered_by",
    ];

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
