<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'exam_session_id',
        'user_id',
        'tryout_package_id',
        'skor_twk',
        'skor_tiu',
        'skor_tkp',
        'skor_total',
        'jumlah_benar',
        'jumlah_salah',
        'jumlah_kosong',
        'category_scores',
    ];

    protected $casts = [
        'skor_twk'        => 'float',
        'skor_tiu'        => 'float',
        'skor_tkp'        => 'float',
        'skor_total'      => 'float',
        'category_scores' => 'array',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tryoutPackage()
    {
        return $this->belongsTo(TryoutPackage::class);
    }
}
