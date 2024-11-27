<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    /** @use HasFactory<\Database\Factories\DriverFactory> */
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'tag',
        'active',
        'category',
        'size',
        'impedance',
        'power',
        'price',
        'link',
        'summary',
        'description',
        'factory_specs',
        'frequency_files',
        'impedance_files',
        'other_files',
        'card_image'
    ];
    protected $casts = [
        'active' => 'boolean',
        'factory_specs' => 'array',
        'frequency_files' => 'array',
        'impedance_files' => 'array',
        'other_files' => 'array',
        'card_image' => 'array'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function designs(): HasMany
    {
        return $this->hasMany(DesignDriver::class);
    }
}
