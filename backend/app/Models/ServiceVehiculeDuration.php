<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceVehiculeDuration extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_id',
        'vehicule_size',
        'duration_minutes',
        'slot_size',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'slot_size' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    // Relations
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
