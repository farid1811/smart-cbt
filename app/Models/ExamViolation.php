<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamViolation extends Model
{
    // Only created_at is present in the table, so disable standard timestamps and handle created_at
    public $timestamps = false;

    protected $fillable = [
        'exam_session_id',
        'violation_type',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Boot the model to automatically set created_at on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * Get the exam session that owns this violation.
     */
    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}
