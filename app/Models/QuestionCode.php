<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCode extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'name', 'code'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'question_code_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'question_code_id');
    }

    public function learningModules()
    {
        return $this->hasMany(LearningModule::class, 'question_code_id');
    }
}
