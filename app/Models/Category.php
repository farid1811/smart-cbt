<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'question_code_id'];

    public function questionCode()
    {
        return $this->belongsTo(QuestionCode::class, 'question_code_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function learningModules()
    {
        return $this->hasMany(LearningModule::class);
    }
}