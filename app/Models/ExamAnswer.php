<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'exam_session_id',
        'question_id',
        'jawaban',
        'is_ragu',
        'options_mapping',
    ];

    protected $casts = [
        'is_ragu'         => 'boolean',
        'options_mapping' => 'array',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function isBenar(): bool
    {
        $visualKey = $this->jawaban;
        if (!$visualKey) {
            return false;
        }

        $mapping = $this->options_mapping;
        $originalKey = ($mapping && isset($mapping[$visualKey])) ? $mapping[$visualKey] : $visualKey;

        return $originalKey === $this->question->jawaban_benar;
    }
}
