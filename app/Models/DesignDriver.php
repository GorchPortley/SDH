<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DesignDriver extends Pivot
{
    protected $fillable = [
            'design_id',
            'driver_id',
            'position',
            'quantity',
            'low_frequency',
            'high_frequency',
            'air_volume',
            'description',
            'specifications'
        ];

    protected $casts = [
        'specifications' => 'array'
    ];

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class, 'id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'id');
    }
}
