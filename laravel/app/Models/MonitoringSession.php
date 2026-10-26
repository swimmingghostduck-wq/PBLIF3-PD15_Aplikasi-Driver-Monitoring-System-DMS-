<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'start_time',
        'end_time',
        'total_drowsy',
        'total_distraction',
        'total_yawn',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_active' => 'boolean',
            'total_drowsy' => 'integer',
            'total_distraction' => 'integer',
            'total_yawn' => 'integer',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(AlertLog::class, 'session_id');
    }
}

