<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TryoutPackage extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'jenis_ujian',
        'durasi_menit',
        'is_active',
        'mulai_at',
        'selesai_at',
        'exam_mode',
        'seb_url',
        'seb_quit_password',
        'seb_browser_lockdown',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'mulai_at'   => 'datetime',
        'selesai_at' => 'datetime',
        'seb_browser_lockdown' => 'boolean',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'tryout_package_questions')
                    ->withPivot('urutan')
                    ->orderBy('tryout_package_questions.urutan')
                    ->withTimestamps();
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        $now = Carbon::now();
        if ($this->mulai_at && $now->lt($this->mulai_at)) return false;
        if ($this->selesai_at && $now->gt($this->selesai_at)) return false;
        return true;
    }

    public function getTotalSoalAttribute(): int
    {
        return $this->questions()->count();
    }
}
