<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class License extends Model
{
    use HasFactory;

    protected $fillable = ['expiry_date', 'is_active'];

    /**
     * Check if the license is valid.
     */
    public function isValid(): bool
    {
        return $this->is_active && Carbon::parse($this->expiry_date)->isFuture();
    }
}
