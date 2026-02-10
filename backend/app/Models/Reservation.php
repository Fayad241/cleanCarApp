<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'reservation_number',
        'user_id',
        'vehicule_id',
        'service_id',
        'scheduled_date',
        'scheduled_time',
        'duration_minutes',
        'slots_occupied',
        'price',
        'status',
        'payment_status',
        'payment_method',
        'special_instructions',
        'source',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'slots_occupied' => 'integer',
        ];
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
