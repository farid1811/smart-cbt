<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'group_id',
        'question_code_id',
        'category_id',
        'sub_category_id',
        'tryout_package_id',
        'urutan',
        'soal',
        'image',
        'question_image',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'option_a_image',
        'option_b_image',
        'option_c_image',
        'option_d_image',
        'option_e_image',
        'score_a',
        'score_b',
        'score_c',
        'score_d',
        'score_e',
        'jawaban_benar',
        'pembahasan',
        'explanation_image',
        'tingkat_kesulitan',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function questionCode()
    {
        return $this->belongsTo(QuestionCode::class, 'question_code_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function tryoutPackage()
    {
        return $this->belongsTo(TryoutPackage::class, 'tryout_package_id');
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
