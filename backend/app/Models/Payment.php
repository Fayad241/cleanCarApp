<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reservation_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    // Relations
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
