<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliahs';

    protected $fillable = ['nama_mk', 'sks'];

    /**
     * Relasi ke model MataKuliahDosen
     */
    public function dosens()
    {
        return $this->belongsToMany(Dosen::class, 'mata_kuliah_dosens', 'mata_kuliah_id', 'dosen_id');
    }

    /**
     * Relasi ke model PenilaianDosen
     */
    public function penilaianDosens()
    {
        return $this->hasMany(PenilaianDosen::class, 'mk_id');
    }

    /**
     * Relasi ke model FinalScore
     */
    public function finalScores()
    {
        return $this->hasMany(FinalScore::class, 'mata_kuliah_id');
    }

    /**
     * Get average score for this mata kuliah
     */
    public function getAverageScoreAttribute()
    {
        return $this->finalScores()->avg('final_score');
    }

    /**
     * Get total evaluations count
     */
    public function getTotalEvaluationsAttribute()
    {
        return $this->penilaianDosens()->count();
    }
}
