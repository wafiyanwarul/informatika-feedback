<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'surveys';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori_id'
    ];

    public function kategoriSurvey()
    {
        return $this->belongsTo(KategoriSurvey::class, 'kategori_id');
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }
}
