<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Design extends Model
{
    /** @use HasFactory<\Database\Factories\DesignFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'tag',
        'active',
        'category',
        'price',
        'build_cost',
        'impedance',
        'power',
        'summary',
        'description',
        'bill_of_materials'
    ];

    protected $casts = [
        'active' => 'boolean',
        'bill_of_materials' => 'array',
    ];

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
