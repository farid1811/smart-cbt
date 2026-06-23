<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'package_id',
        'attempt_number',
        'score',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'score' => 'double',
    ];

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function package()
    {
        return $this->belongsTo(TryoutPackage::class, 'package_id');
    }
}
