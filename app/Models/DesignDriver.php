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
            'specifications',
            'frequency_files',
            'impedance_files',
            'other_files'
        ];

    protected $casts = [
        'specifications' => 'array',
        'frequency_files' => 'array',
        'impedance_files' => 'array',
        'other_files' => 'array'
    ];

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
