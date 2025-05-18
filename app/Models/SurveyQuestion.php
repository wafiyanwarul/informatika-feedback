<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'pertanyaan',
        'tipe',
    ];

    /**
     * Relasi: Pertanyaan ini milik sebuah survey.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Relasi: Pertanyaan ini memiliki banyak response (jawaban user).
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'question_id');
    }
}
