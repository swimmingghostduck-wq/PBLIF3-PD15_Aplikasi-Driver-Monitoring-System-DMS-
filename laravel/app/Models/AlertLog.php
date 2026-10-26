<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'driver_id',
        'alert_type',
        'severity',
        'duration_seconds',
        'snapshot_path',
        'timestamp',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'float',
            'timestamp' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(MonitoringSession::class, 'session_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Sanitasi catatan telemetri operasional untuk admin (menghapus jargon teknis AI)
     */
    public function getCleanNotesAttribute(): string
    {
        $notes = $this->notes ?? '';
        if (trim($notes) === '' || trim($notes) === '-') {
            return match ($this->alert_type) {
                'kantuk' => 'Mata terpejam saat berkendara',
                'tidak_fokus' => 'Pandangan teralihkan dari arah jalan',
                'menguap' => 'Kelelahan terdeteksi',
                default => 'Insiden kewaspadaan terdeteksi',
            };
        }

        // Filter out ML technical jargon
        $cleaned = preg_replace('/Metode:\s*[^|;]+[|;]?/i', '', $notes);
        $cleaned = preg_replace('/\(Nilai EAR:[^)]+\)/i', '', $cleaned);
        $cleaned = preg_replace('/\(Nilai MAR:[^)]+\)/i', '', $cleaned);
        $cleaned = preg_replace('/\(Sudut Pitch:[^)]+\)/i', '', $cleaned);
        $cleaned = preg_replace('/\(Sudut Yaw:[^)]+\)/i', '', $cleaned);
        $cleaned = preg_replace('/EAR:\s*[\d.]+/i', '', $cleaned);
        $cleaned = preg_replace('/MAR:\s*[\d.]+/i', '', $cleaned);
        $cleaned = preg_replace('/Yaw:\s*[^|;]+/i', '', $cleaned);
        $cleaned = preg_replace('/Pitch:\s*[^|;]+/i', '', $cleaned);
        $cleaned = trim(str_replace(['|', ';'], '', $cleaned));

        if (empty($cleaned)) {
            return match ($this->alert_type) {
                'kantuk' => 'Mata terpejam saat berkendara',
                'tidak_fokus' => 'Pandangan teralihkan dari arah jalan',
                'menguap' => 'Kelelahan terdeteksi',
                default => 'Insiden kewaspadaan terdeteksi',
            };
        }

        return $cleaned;
    }
}

