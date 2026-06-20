<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'category_id',
        'soal',
        'image',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'jawaban_benar',
        'pembahasan',
        'tingkat_kesulitan',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tryoutPackages()
    {
        return $this->belongsToMany(TryoutPackage::class, 'tryout_package_questions')
                    ->withPivot('urutan')
                    ->withTimestamps();
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function getOpsiLabelAttribute(): array
    {
        $opsi = [
            'A' => $this->opsi_a,
            'B' => $this->opsi_b,
            'C' => $this->opsi_c,
            'D' => $this->opsi_d,
        ];
        if ($this->opsi_e) {
            $opsi['E'] = $this->opsi_e;
        }
        return $opsi;
    }
}
