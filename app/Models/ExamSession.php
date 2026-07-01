<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamSession extends Model
{
    protected $fillable = [
        'user_id',
        'tryout_package_id',
        'started_at',
        'ended_at',
        'durasi_detik',
        'status',
        'soal_order',
        'violations_count',
        'violation_logs',
    ];

    protected $casts = [
        'started_at'     => 'datetime',
        'ended_at'       => 'datetime',
        'soal_order'     => 'array',
        'violation_logs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tryoutPackage()
    {
        return $this->belongsTo(TryoutPackage::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class);
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }

    public function getRemainingSecondsAttribute(): int
    {
        $totalDetik = $this->tryoutPackage->durasi_menit * 60;

        if ($this->started_at) {
            $elapsed = Carbon::now()->diffInSeconds($this->started_at, true);
            if (Carbon::now()->lt($this->started_at)) {
                $elapsed = 0;
            }
        } else {
            $elapsed = 0;
        }

        return max(0, $totalDetik - $elapsed);
    }

    public function isExpired(): bool
    {
        return $this->remaining_seconds <= 0;
    }
}
