<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'question_code_id',
        'category_id',
        'name',
        'description',
        'pdf_file',
        'video_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}
