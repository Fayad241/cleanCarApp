<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'vehicule_type',
        'is_active',
        'is_popular',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'order' => 'integer',
        ];
    }

    // Relations
    public function durations()
    {
        return $this->hasMany(ServiceVehiculeDuration::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
